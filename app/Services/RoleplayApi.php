<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RoleplayApi
{
    protected string $base;

    public function __construct()
    {
        $this->base = rtrim(config('services.roleplay.url', env('ROLEPLAY_API_URL', 'http://127.0.0.1:8601')), '/');
    }

    public function start(): array
    {
        $r = Http::timeout(20)->post($this->base.'/start');
        $r->throw();
        return $r->json();
    }

    public function turn(array $history, string $candidateText): array
    {
        $payload = [
            'history'        => $history, // [{role:'buyer'|'candidate', text:'...'}]
            'candidate_text' => $candidateText,
        ];
        $r = Http::timeout(30)->post($this->base.'/turn', $payload);
        $r->throw();
        return $r->json();
    }

    public function evaluate(array $transcript): array
    {
        $r = Http::timeout(30)->post($this->base.'/evaluate', ['transcript' => $transcript]);
        $r->throw();
        return $r->json();
    }
	    public function init(array $config): array { return ['ok' => true]; }
    public function score(array $payload): array {
        return [
            'score' => 82.0,
            'rubric' => ['discovery'=>25,'qualification'=>16,'objections'=>28,'close'=>13],
            'highlights' => ['Great probing on surgeon workflow'],
            'flags' => ['Weak close'],
        ];
    }
}
