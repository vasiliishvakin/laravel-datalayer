<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Data;
use Vaskiq\LaravelDataLayer\Contracts\DataFactoryInterface;

/**
 * @template TData of Data
 * @template TModel of Model
 *
 * @extends AbstractRepository<TData, TModel>
 */
abstract class EloquentRepository extends AbstractRepository
{
    /** @var class-string<TModel> */
    protected readonly string $modelClass;

    /**
     * @param  TModel  $model
     */
    public function __construct(
        protected readonly Model $model,
        DataFactoryInterface $dataFactory,
    ) {
        parent::__construct($dataFactory);
        $this->modelClass = get_class($model);
    }

    /**
     * @return TData|null
     */
    public function find(string|int $id): ?Data
    {
        $model = $this->model->find($id);

        return $model ? $this->toData($model) : null;
    }

    /**
     * @return Collection<string|int, TData>
     */
    public function all(): Collection
    {
        $items = $this->model->all();

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

        $model = isset($fields[$keyName])
            ? $this->model->find($fields[$keyName]) ?? $this->model()
            : $this->model();

        $model = $this->fillFromArray($model, $fields);

        $model->save();

        return $this->toData($model);
    }

    /**
     * @param  array<string, mixed>  $attributes  Array of attributes where keys are field names and values are their corresponding values.
     * @return TData|null
     */
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

    /**
     * @param  TData  $data
     * @param  array<string>  $relations  Array of relation names to load.
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
     * @return Builder<TModel>
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function raw(): \Illuminate\Database\Query\Builder
    {
        return DB::table($this->model->getTable());
    }

    /**
     * @param  TModel  $model
     * @param  TData  $data
     * @return TModel
     */
    protected function fill(Model $model, Data $data): Model
    {
        $model->fill($data->toArray());

        return $model;
    }

    /**
     * @param  TModel  $model
     * @param  array<string, mixed>  $data
     * @return TModel
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
