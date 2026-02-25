<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class FirebaseUser implements Authenticatable
{
    public function __construct(
        public string $firebase_uid,
        public ?string $email = null,
        public ?string $name = null,
        public array $profile = [],
    ) {
    }

    public static function fromProfile(string $uid, array $profile): self
    {
        return new self(
            firebase_uid: $uid,
            email: isset($profile['email']) ? (string) $profile['email'] : null,
            name: isset($profile['name']) ? (string) $profile['name'] : null,
            profile: $profile,
        );
    }

    public function getAuthIdentifierName(): string
    {
        return 'firebase_uid';
    }

    public function getAuthIdentifier(): string
    {
        return $this->firebase_uid;
    }

    public function getAuthPassword(): ?string
    {
        return null;
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
}
