<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Vaskiq\LaravelDataLayer\Contracts\DataFactoryInterface;

abstract class AbstractEloquentRepository extends AbstractRepository
{
    protected readonly string $modelClass;

    public function __construct(
        DataFactoryInterface $dataFactory,
        protected readonly Model $model,
    ) {
        parent::__construct($dataFactory);
        $this->modelClass = get_class($model);
    }

    protected function fill(Model $model, Data $data): Model
    {
        $model->fill($data->toArray());

        return $model;
    }

    protected function fillFromArray(Model $model, array $data): Model
    {
        $model->fill($data);

        return $model;
    }

    protected function model(): Model
    {
        return new $this->modelClass;
    }

    protected function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function find(string|int $id): ?Data
    {
        $model = $this->model->find($id);

        return $model ? $this->toData($model) : null;
    }

    /** @return Collection<string|int, Data> */
    public function all(): Collection
    {
        $items = $this->model->all();

        return $this->toDataCollection($items);
    }

    /** @return Data|Collection<string|int, Data>|null */
    public function findBy(string $field, mixed $value, bool $onlyFirst = false): Data|Collection|null
    {
        $query = $this->model()->where($field, $value);

        if ($onlyFirst) {
            $result = $query->first();

            return $result ? $this->toData($result) : null;
        }

        $items = $query->get();

        return $items->isNotEmpty() ? $this->toDataCollection($items) : collect();
    }

    public function save(Data $data): Data
    {
        $keyName = $this->model->getKeyName();
        $fields = $data->toArray();

        $model = isset($fields[$keyName])
            ? $this->model->find($fields[$keyName]) ?? $this->model()
            : $this->model();

        $model = $this->fillFromArray($model, $fields);

        $model->save();

        return $this->toData($model);
    }

    public function update(string|int $id, array $attributes): ?Data
    {
        $model = $this->model->find($id);

        if (! $model) {
            return null;
        }

        $this->fillFromArray($model, $attributes)->save();

        return $this->toData($model);
    }

    public function delete(string|int $id): bool
    {
        return (bool) $this->model->whereKey($id)->delete();
    }

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
}
