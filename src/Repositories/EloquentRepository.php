<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Vaskiq\LaravelDataLayer\Contracts\DataFactoryInterface;

/**
 * @template TData of Data
 * @template TModel of Model
 *
 * @extends EloquentModelRepository<TModel>
 */
abstract class EloquentRepository extends EloquentModelRepository
{
    /** @var class-string<TModel> */
    protected readonly string $modelClass;

    /**
     * @param  TModel  $model
     */
    public function __construct(
        Model $model,
        protected readonly DataFactoryInterface $dataFactory,
    ) {
        parent::__construct($model);
        $this->modelClass = get_class($model);
    }

    /**
     * @return class-string<TData>
     */
    abstract public function dataClass(): string;

    /**
     * @return TData|null
     */
    public function find(string|int $id): ?Data
    {
        $model = $this->findModel($id);

        return $model ? $this->toData($model) : null;
    }

    /**
     * @return Collection<string|int, TData>
     */
    public function all(): Collection
    {
        $items = $this->allModels();

        return $this->toDataCollection($items);
    }

    /**
     * @return TData|Collection<string|int, TData>|null
     */
    public function findBy(string $field, mixed $value, bool $onlyFirst = false): Data|Collection|null
    {
        $query = $this->query()
            ->when(is_null($value), fn ($q) => $q->whereNull($field))
            ->when(
                is_array($value),
                fn ($q) => $q->whereIn($field, $value),
                fn ($q) => $q->where($field, $value)
            );

        if ($onlyFirst) {
            $result = $query->first();

            return $result ? $this->toData($result) : null;
        }

        $items = $query->get();

        return $items->isNotEmpty() ? $this->toDataCollection($items) : collect();
    }

    /**
     * @param  TData  $data
     * @return TData
     */
    public function save(Data $data): Data
    {
        $keyName = $this->model->getKeyName();
        $fields = $data->toArray();

        if (isset($fields[$keyName])) {
            $model = $this->updateModel($fields[$keyName], $fields);
        } else {
            $model = $this->createModel($fields);
        }

        return $this->toData($model);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return TData|null
     */
    public function update(string|int $id, array $attributes): ?Data
    {
        $model = $this->updateModel($id, $attributes);

        return $model ? $this->toData($model) : null;
    }

    /**
     * @param  TData  $data
     * @param  array<string>  $relations
     * @return TData
     */
    public function loadRelations(Data $data, array $relations): Data
    {
        $keyName = $this->model->getKeyName();
        $id = $data->{$keyName} ?? null;

        if ($id === null) {
            return $data;
        }

        $model = $this->query()->with($relations)->find($id);

        return $model ? $this->toData($model) : $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function empty(): array
    {
        return $this->dataFactory->empty($this->dataClass());
    }

    public function new(mixed $data): Data
    {
        return $this->toData($data);
    }

    /**
     * @return TData
     */
    public function toData(mixed $source): Data
    {
        return $this->dataFactory->create($source, $this->dataClass());
    }

    public function delete(string|int $id): bool
    {
        return $this->deleteModel($id);
    }

    /**
     * @param  Collection<int, TModel>  $models
     * @return Collection<int, TData>
     */
    protected function toDataCollection(Collection $models): Collection
    {
        return $this->dataFactory->map($models, $this->dataClass());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function fillFromArray(Model $model, array $data): Model
    {
        $model->fill($data);

        return $model;
    }

    /**
     * @return TModel
     */
    protected function model(): Model
    {
        return new $this->modelClass;
    }
}
