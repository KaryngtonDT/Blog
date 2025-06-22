<?php

namespace App\Module\User\Service;

use App\Module\User\Entity\User;

interface AuthServiceInterface
{
    public function login(string $email, string $password): bool;
    public function register(string $email, string $password): false|int;

    public function logout(): void;

    public function isLoggedIn(): bool;

    public function isAdmin(): bool;

    public function user(): ?User;
}
