<?php

namespace App\Framework\Repository;

use Pagerfanta\Pagerfanta;

interface RepositoryInterface
{
    public function findPaginated(int $perpage, int $currentpage): Pagerfanta;

    public function findAll(): ?array;

    public function findBy(string $key, $value): ?object;

    public function insert(array $data): int;

    public function update(int $id, array $data): void;

    public function delete(int $id): void;

    public function hydrate(array $data): ?object;
}
