<?php

namespace Tests\Unit\Stubs;

use App\Contracts\Repositories\IUserRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\Hash;

class UserRepositoryStub implements IUserRepository
{
    /**
     * @var User[]|array
     */
    private static array $users = [];
    private static int $nextId = 3;

    public function __construct()
    {
        if (empty(self::$users)) {
            self::$users = [
                1 => new User([
                                  'id' => 1,
                                  'name' => 'test-1',
                                  'email' => 'test-1@example.com',
                                  'password' => bcrypt('password'),
                              ]),
                2 => new User([
                                  'id' => 2,
                                  'name' => 'test-2',
                                  'email' => 'test-2@example.com',
                                  'password' => bcrypt('password'),
                              ]),
            ];
        }
    }

    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        $collection = new BaseCollection(array_values(self::$users));
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $pagedData = $collection->forPage($currentPage, $perPage);

        return new Paginator($pagedData, $collection->count(), $perPage, $currentPage, [
            'path' => Paginator::resolveCurrentPath(),
        ]);
    }

    public function getBy(string $column, string $operator, $value): EloquentCollection
    {
        $filtered = array_filter(self::$users, fn($user) => $user->{$column} === $value);
        return new EloquentCollection($filtered);
    }

    public function findById(int|string $id): User
    {
        if (!isset(self::$users[$id])) {
            abort(404, "User not found.");
        }

        return self::$users[$id];
    }

    public function create(array $data): User
    {
        $data['id'] = self::$nextId++;
        $data['password'] = Hash::make($data['password']);

        $user = new User($data);
        self::$users[$user->id] = $user;
        return $user;
    }

    public function update(int|string $id, array $data): User
    {
        if (!isset(self::$users[$id])) {
            abort(404, "User not found.");
        }

        $user = self::$users[$id];

        foreach ($data as $key => $value) {
            $user->{$key} = $value;
        }

        self::$users[$id] = $user;

        return $user;
    }

    public function delete(int|string $id): bool
    {
        if (!isset(self::$users[$id])) {
            abort(404, "User not found.");
        }

        unset(self::$users[$id]);
        return true;
    }
}
