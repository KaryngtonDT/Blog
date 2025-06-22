<?php

namespace App\Framework\Service;

interface FlashServiceInterface
{
    public function add(string $type, string $message): void;

    public function get(string $type): ?string;
}
