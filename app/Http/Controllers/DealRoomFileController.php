<?php

namespace App\Http\Controllers;

use App\Models\DealRoomFile;
use App\Models\DealRoomFileDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class DealRoomFileController extends Controller
{
    public function download(Request $request, DealRoomFile $file)
    {
        // Authorization: user must belong to one of the room companies
        $user = Auth::user();
        abort_unless($user, 401);

        $companyId = null;
        if (method_exists($user, 'currentTeam') && $user->currentTeam) {
            $companyId = (int) $user->currentTeam->id;
        }

        $room = $file->room()->with(['companySmall', 'companyLarge'])->firstOrFail();
        $allowed = $companyId && (
            (int)$room->company_small_id === $companyId ||
            (int)$room->company_large_id === $companyId
        );

        abort_unless($allowed, 403);

        // Log audit trail
        DealRoomFileDownload::create([
            'file_id'    => $file->id,
            'user_id'    => $user->id,
            'company_id' => $companyId,
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
        ]);

        // Download
        if (! Storage::exists($file->path)) {
            abort(404, 'File missing');
        }

        return Storage::download($file->path, $file->name);
    }
}
