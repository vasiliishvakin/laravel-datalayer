<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Repositories;

use Closure;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Vaskiq\LaravelDataLayer\Contracts\EloquentModelRepositoryInterface;

abstract class EloquentModelRepository implements EloquentModelRepositoryInterface
{

    public function __construct(protected readonly Model $model)
    {

    }

    public function findModel(int|string $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findModelOrFail(int|string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function allModels(): Collection
    {
        return $this->model->all();
    }

    public function createModel(array $data): Model
    {
        return $this->model->create($data);
    }

    public function updateModel(int|string $id, array $data): ?Model
    {
        $model = $this->findModel($id);
        if ($model) {
            $model->update($data);
        }

        return $model;
    }

    public function deleteModel(int|string $id): bool
    {
        $model = $this->findModel($id);

        return $model ? $model->delete() : false;
    }

    public function query(): EloquentBuilder
    {
        return $this->model->newQuery();
    }

    public function raw(): QueryBuilder
    {
        return $this->model->getConnection()->table($this->model->getTable());
    }

    public function modelClass(): string
    {
        return get_class($this->model);
    }

    public function newModel(): Model
    {
        return new $this->model;
    }

    public function updateOrCreateModel(array $attributes, array $values): Model
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    public function deleteAllModelsBy(array|Closure|Expression $conditions): int
    {
        return (int) $this->model->where($conditions)->delete();
    }

    public function findModelBy(array|Closure|Expression $conditions): ?Model
    {
        return $this->model->where($conditions)->first();
    }

    public function findAllModelsBy(array|Closure|Expression $conditions): Collection
    {
        return $this->model->where($conditions)->get();
    }
}
