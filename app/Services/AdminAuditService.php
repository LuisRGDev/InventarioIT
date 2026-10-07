<?php

namespace App\Services;

use App\Models\AdminAudit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminAuditService
{
    public function __construct(private Request $request) {}

    /**
     * @param  array<string, mixed>  $details
     */
    public function record(User $actor, string $action, ?User $subject = null, array $details = []): AdminAudit
    {
        return AdminAudit::create([
            'actor_id' => $actor->id,
            'actor_email' => Str::limit($actor->email, 255, ''),
            'subject_id' => $subject?->id,
            'subject_email' => $subject ? Str::limit($subject->email, 255, '') : null,
            'action' => $action,
            'details' => $details ?: null,
            'ip_address' => $this->request->ip(),
            'created_at' => now(),
        ]);
    }
}
