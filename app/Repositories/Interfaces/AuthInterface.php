<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

interface AuthInterface
{
    public function issueToken(User $user, string $tokenName = 'sanctum-token'): string;
    public function revokeToken(PersonalAccessToken $token): bool;
}
