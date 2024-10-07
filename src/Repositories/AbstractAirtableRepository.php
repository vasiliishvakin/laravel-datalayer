<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Repositories;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Vaskiq\LaravelDataLayer\Contracts\DataFactoryInterface;
use Vaskiq\LaravelDataLayer\Models\AirtableModel;

abstract class AbstractAirtableRepository extends AbstractRepository
{
    public function __construct(
        DataFactoryInterface $dataFactory,
        protected readonly AirtableModel $model,
    ) {
        parent::__construct($dataFactory);
    }

    public function find(string|int $id): ?Data
    {
        $record = $this->model->find($id);

        if ($record === null) {
            return null;
        }

        return $this->toData($record);
    }

    /** @return Collection<int|string|int, Data> */
    public function all(): Collection
    {
        $records = $this->model->all();
        $collection = Collection::make($records);

        return $this->toDataCollection($collection);
    }

    /** @return Data|null|Collection<string|int, Data> */
    public function findBy(string $field, mixed $value, bool $onlyFirst = false): Data|Collection|null
    {
        $records = $this->model->where($field, $value)->get();

        return $records;
    }

    public function save(Data $data): Data
    {
        $keyName = $this->model->getKeyName();
        $fields = $data->toArray();

        $item = isset($fields[$keyName])
            ? $this->model->find($fields[$keyName]) ?? null
            : null;

        $result = ($item === null)
            ? $this->model->create($fields)
            : $this->update($fields[$keyName], $fields);

        return $result;
    }

    public function update(string|int $id, array $attributes): ?Data
    {
        $record = $this->model->find($id);

        if ($record === null) {
            return null;
        }

        $record->update($attributes);

        return $this->toData($record);
    }

    public function delete(string|int $id): bool
    {
        $record = $this->model->find($id);

        if ($record === null) {
            return false;
        }

        return $this->model->destroy($id);
    }
}
