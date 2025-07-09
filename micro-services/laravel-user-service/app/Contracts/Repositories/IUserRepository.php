<?php

namespace App\Contracts\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\User;

interface IUserRepository
{
    public function getAll(int $perPage = 15): LengthAwarePaginator;

    public function getBy(string $column, string $operator, mixed $value): Collection;

    public function findById(int|string $id): User;

    public function create(array $data): User;

    public function update(int|string $id, array $data): User;

    public function delete(int|string $id): bool;
}
