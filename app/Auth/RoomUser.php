<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class RoomUser implements Authenticatable
{
    public function __construct(
        public string $room_id,
        public string $username,
        public array $profile = [],
    ) {
    }

    public static function fromProfile(string $roomId, array $profile): self
    {
        $username = isset($profile['username']) ? (string) $profile['username'] : $roomId;

        return new self(
            room_id: $roomId,
            username: $username,
            profile: $profile,
        );
    }

    public function getAuthIdentifierName(): string
    {
        return 'room_id';
    }

    public function getAuthIdentifier(): string
    {
        return $this->room_id;
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
