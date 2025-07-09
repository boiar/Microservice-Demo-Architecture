<?php

namespace App\Repositories;

use App\Contracts\Repositories\IUserRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserRepository implements IUserRepository
{
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return User::paginate($perPage);
    }

    public function getBy(string $column, string $operator, mixed $value): Collection
    {
        return User::where($column, $operator, $value)->get();
    }

    public function findById(int|string $id): User
    {
        return User::findOrFail($id);
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function update(int|string $id, array $data): User
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete(int|string $id): bool
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }
}
