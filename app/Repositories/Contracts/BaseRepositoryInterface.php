<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Get all records
     */
    public function all(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Get paginated records
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], array $relations = []): LengthAwarePaginator;

    /**
     * Find record by ID
     */
    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Find record by ID or fail
     */
    public function findOrFail(int $id, array $columns = ['*'], array $relations = []): Model;

    /**
     * Find by specific column
     */
    public function findBy(string $column, $value, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Find all by specific column
     */
    public function findAllBy(string $column, $value, array $columns = ['*'], array $relations = []): Collection;

    /**
     * Create new record
     */
    public function create(array $data): Model;

    /**
     * Update existing record
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete record
     */
    public function delete(int $id): bool;

    /**
     * Get records with where clause
     */
    public function where(array $conditions, array $columns = ['*'], array $relations = []): Collection;

    /**
     * Get first record with where clause
     */
    public function firstWhere(array $conditions, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Count records
     */
    public function count(array $conditions = []): int;

    /**
     * Check if record exists
     */
    public function exists(array $conditions): bool;
}
