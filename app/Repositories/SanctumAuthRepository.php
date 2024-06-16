<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\AuthInterface;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumAuthRepository implements AuthInterface
{
    public function issueToken(User $user, string $tokenName = 'sanctum-token'): string
    {
        return $user->createToken($tokenName)->plainTextToken;
    }

    public function revokeToken(PersonalAccessToken $token): bool
    {
        return $token->delete() ? 1 : 0;
    }
}
