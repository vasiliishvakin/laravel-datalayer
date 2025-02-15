<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Contracts;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Closure;
use Illuminate\Contracts\Database\Query\Expression;

/**
 * @template TModel of Model
 */
interface EloquentModelRepositoryInterface
{
    /**
     * Finds an Eloquent model by its ID.
     *
     * @param int|string $id
     * @return TModel|null
     */
    public function findModel(int|string $id): ?Model;

    /**
     * Finds an Eloquent model by its ID or throws an exception.
     *
     * @param int|string $id
     * @return TModel
     */
    public function findModelOrFail(int|string $id): Model;

    /**
     * Returns a collection of all Eloquent models.
     *
     * @return Collection<int|string, TModel>
     */
    public function allModels(): Collection;

    /**
     * Creates and persists a new Eloquent model.
     *
     * @param array<string, mixed> $data
     * @return TModel
     */
    public function createModel(array $data): Model;

    /**
     * Updates an Eloquent model by its ID and returns the updated model or null if not found.
     *
     * @param int|string $id
     * @param array<string, mixed> $data
     * @return TModel|null
     */
    public function updateModel(int|string $id, array $data): ?Model;

    /**
     * Deletes an Eloquent model by its ID.
     *
     * @param int|string $id
     * @return bool
     */
    public function deleteModel(int|string $id): bool;

    /**
     * Returns an Eloquent Builder instance for flexible query building.
     *
     * @return EloquentBuilder<TModel>
     */
    public function query(): EloquentBuilder;

    /**
     * Returns a raw Query Builder instance using DB::table(...).
     *
     * @return QueryBuilder
     */
    public function raw(): QueryBuilder;

    /**
     * Returns the fully qualified class name of the Eloquent model.
     *
     * @return class-string<TModel>
     */
    public function modelClass(): string;

    /**
     * Creates a new instance of the Eloquent model without persisting it.
     *
     * @return TModel
     */
    public function newModel(): Model;

    /**
     * Updates an existing model or creates a new one based on the provided attributes.
     *
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $values
     * @return TModel
     */
    public function updateOrCreateModel(array $attributes, array $values): Model;

    /**
     * Deletes all Eloquent models that match the given conditions.
     *
     * @param array|Closure|Expression $conditions
     * @return int The number of records deleted.
     */
    public function deleteAllModelsBy(array|Closure|Expression $conditions): int;

    /**
     * Finds a single Eloquent model based on a set of conditions.
     *
     * @param array|Closure|Expression $conditions
     * @return TModel|null
     */
    public function findModelBy(array|Closure|Expression $conditions): ?Model;

    /**
     * Finds all Eloquent models based on a set of conditions.
     *
     * @param array|Closure|Expression $conditions
     * @return Collection<int|string, TModel>
     */
    public function findAllModelsBy(array|Closure|Expression $conditions): Collection;
}
