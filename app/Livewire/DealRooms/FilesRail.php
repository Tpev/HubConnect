<?php

namespace App\Livewire\DealRooms;

use App\Models\DealRoom;
use App\Models\DealRoomFile;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class FilesRail extends Component
{
    use WithFileUploads;

    public DealRoom $room;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    #[Validate([
        'upload' => 'nullable|file|max:51200', // 50 MB (adjust if needed)
    ])]
    public $upload = null;

    public ?string $previewUrl = null;
    public ?int $previewId = null;

    public function mount(DealRoom $room): void
    {
        // Basic access guard: must be a participant
        $companyId = $this->currentCompanyId();
        if (!$companyId || !$room->includesCompany($companyId)) {
            abort(403);
        }

        $this->room = $room->load(['files.uploader']);
    }

    #[On('deal-files:refresh')]
    public function refreshFiles(): void
    {
        $this->room->load(['files.uploader']);
    }

    /** Auto-run after user selects a file (TallStackUI triggers change) */
    public function updatedUpload(): void
    {
        $this->uploadFile();
    }

    public function uploadFile(): void
    {
        $this->validate();

        if (!$this->upload) {
            return;
        }

        // Store on the "public" disk so we can preview via URL
        $path = $this->upload->store("deal-rooms/{$this->room->id}", 'public');

        DealRoomFile::create([
            'room_id'     => $this->room->id,
            'path'        => $path,                                       // string column
            'name'        => $this->upload->getClientOriginalName(),      // string column
            'type'        => $this->upload->getMimeType(),                // nullable string
            'size'        => (int) ($this->upload->getSize() ?: 0),       // unsigned bigint
            'uploaded_by' => Auth::id(),                                  // FK to users
        ]);

        $this->reset('upload');

        // Optional: toast event (if you have a global listener)
        $this->dispatch('toast', [
            'type' => 'success',
            'text' => 'File uploaded.',
        ]);

        $this->refreshFiles();
        $this->dispatch('deal-files:uploaded');
    }

    public function preview(int $id): void
    {
        /** @var DealRoomFile|null $file */
        $file = $this->room->files()->whereKey($id)->first();
        if (!$file) {
            return;
        }

        // Ensure file exists on disk
        if (!Storage::disk('public')->exists($file->path)) {
            $this->dispatch('toast', ['type' => 'error', 'text' => 'File not found on storage.']);
            return;
        }

        $this->previewId  = $file->id;
        $this->previewUrl = Storage::disk('public')->url($file->path);
    }

    public function closePreview(): void
    {
        $this->previewUrl = null;
        $this->previewId  = null;
    }

    public function download(int $id)
    {
        /** @var DealRoomFile|null $file */
        $file = $this->room->files()->whereKey($id)->first();
        if (!$file) {
            abort(404);
        }

        try {
            $fullPath = Storage::disk('public')->path($file->path);
            if (!is_file($fullPath)) {
                abort(404);
            }
        } catch (FileNotFoundException $e) {
            abort(404);
        }

        $name = $file->name ?: basename($file->path);

        // If you want, log an audit row here (e.g., DealRoomFileDownload::create([...]);)

        return response()->streamDownload(function () use ($fullPath) {
            readfile($fullPath);
        }, $name, [
            'Content-Type' => $file->type ?: 'application/octet-stream',
        ]);
    }

    public function remove(int $id): void
    {
        /** @var DealRoomFile|null $file */
        $file = $this->room->files()->whereKey($id)->first();
        if (!$file) {
            return;
        }

        // Basic "authorization": only participants can delete; optionally restrict to uploader
        $companyId = $this->currentCompanyId();
        if (!$companyId || !$this->room->includesCompany($companyId)) {
            abort(403);
        }

        // Delete from disk if present
        if ($file->path && Storage::disk('public')->exists($file->path)) {
            Storage::disk('public')->delete($file->path);
        }

        $file->delete();

        $this->dispatch('toast', [
            'type' => 'success',
            'text' => 'File deleted.',
        ]);

        $this->refreshFiles();
        $this->dispatch('deal-files:refresh');
    }

    public function render()
    {
        return view('livewire.deal-rooms.files-rail', [
            'files' => $this->room->files()->latest('created_at')->get(),
        ]);
    }

    /** Helper: get current user company id (mirrors your other components) */
    protected function currentCompanyId(): ?int
    {
        $user = Auth::user();
        if (!$user) return null;

        if (method_exists($user, 'currentTeam') && $user->currentTeam) {
            return (int) $user->currentTeam->id;
        }
        if (method_exists($user, 'ownedTeams')) {
            $owned = $user->ownedTeams()->first();
            if ($owned) return (int) $owned->id;
        }
        if (method_exists($user, 'teams')) {
            $any = $user->teams()->first();
            if ($any) return (int) $any->id;
        }
        if (isset($user->team_id) && $user->team_id) {
            return (int) $user->team_id;
        }
        if (isset($user->company_id) && $user->company_id) {
            return (int) $user->company_id;
        }

        return null;
    }
}
