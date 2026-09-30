<?php

namespace App\Services;

use App\Models\LoginAudit;
use App\Models\User;
use Illuminate\Http\Request;

class LoginAuditService
{
    public function __construct(private Request $request) {}

    public function record(?User $user, string $email, bool $successful, ?string $reason = null): LoginAudit
    {
        return LoginAudit::create([
            'user_id' => $user?->id,
            'email' => $email,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'successful' => $successful,
            'reason' => $reason,
            'created_at' => now(),
        ]);
    }
}
