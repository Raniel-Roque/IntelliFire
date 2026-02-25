<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Kreait\Firebase\Contract\Database;

class FirebaseUserProvider implements UserProvider
{
    public function __construct(private readonly Database $database)
    {
    }

    public function retrieveById($identifier): ?Authenticatable
    {
        if (!is_string($identifier) || $identifier === '') {
            return null;
        }

        $snapshot = $this->database->getReference('users/'.$identifier)->getSnapshot();
        $value = $snapshot->getValue();

        if (!is_array($value)) {
            return null;
        }

        return FirebaseUser::fromProfile($identifier, $value);
    }

    public function retrieveByToken($identifier, #[\SensitiveParameter] $token): ?Authenticatable
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, #[\SensitiveParameter] $token): void
    {
    }

    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials): ?Authenticatable
    {
        return null;
    }

    public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials): bool
    {
        return false;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false): void
    {
    }
}
