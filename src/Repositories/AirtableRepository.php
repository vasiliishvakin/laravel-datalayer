<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Repositories;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Vaskiq\LaravelDataLayer\Contracts\DataFactoryInterface;
use Vaskiq\LaravelDataLayer\Data\AirtableData;
use Vaskiq\LaravelDataLayer\Errors\AirtableError;
use Vaskiq\LaravelDataLayer\Models\AirtableModel;

abstract class AirtableRepository extends AbstractRepository
{
    public function __construct(
        protected readonly AirtableModel $model,
        DataFactoryInterface $dataFactory,
    ) {
        parent::__construct($dataFactory);
    }

    protected function responseHasError(?Collection $response): bool
    {
        return $response->has('error') && ! empty($response->get('error'));
    }

    protected function responseGetError(?Collection $response): ?AirtableError
    {
        if (! $response->has('error')) {
            return null;
        }

        $error = $response->get('error');
        if (is_array($error)) {
            return new AirtableError(
                message: $error['message'],
                type: $error['type'],
            );
        } else {
            return new AirtableError(
                message: $error,
            );
        }
    }

    protected function isEmptyResponse(?Collection $response): bool
    {
        if ($response === null) {
            return true;
        } elseif ($response instanceof Collection) {
            if ($response->isEmpty()) {
                return true;
            } elseif ($this->responseHasError($response)) {
                $error = $this->responseGetError($response);
                if ($error->message === 'NOT_FOUND') {
                    return true;
                } elseif (
                    $error->type === 'INVALID_PERMISSIONS_OR_MODEL_NOT_FOUND'
                    || $error->type === 'NOT_FOUND'
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    public function find(string|int $id): ?Data
    {
        /** @var Collection */
        $record = $this->model->find($id);

        if ($this->isEmptyResponse($record)) {
            return null;
        }

        $error = $this->responseGetError($record);
        if ($error !== null) {
            throw new \RuntimeException((string) $error);
        }

        $record = $record->toArray();

        return $this->toData($record);
    }

    /** @return Collection<int|string|int, Data> */
    public function all(): Collection
    {
        $records = $this->model->all();

        $error = $this->responseGetError($records);
        if ($error !== null) {
            throw new \RuntimeException((string) $error);
        }

        return $this->toDataCollection($records);
    }

    /** @return Data|null|Collection<string|int, Data> */
    public function findBy(string $field, mixed $value, bool $onlyFirst = false): Data|Collection|null
    {
        $field = $this->dataClass()::mapField($field);

        $records = $this->model->where($field, $value)->get();

        $error = $this->responseGetError($records);
        if ($error !== null) {
            throw new \RuntimeException((string) $error);
        }

        if ($onlyFirst) {
            $record = $records->first();
            if ($record === null) {
                return null;
            }

            return $this->toData($record);
        }

        return $this->toDataCollection($records);
    }

    public function exist(string|int $id): bool
    {
        return ! $this->isEmptyResponse($this->model->find($id));
    }

    public function save(Data $data): Data
    {
        if (! $data instanceof AirtableData) {
            throw new \InvalidArgumentException('Data must be an instance of AirtableData');
        }

        $keyName = $this->model->getKeyName();
        $fields = $data->toAirtableArray();

        $id = $fields[$keyName] ?? null;

        if (array_key_exists($keyName, $fields)) {
            unset($fields[$keyName]);
        }

        $isExist = $id ? $this->exist($id) : false;

        $result = ($isExist)
            ? $this->update($id, $fields, false)
            : $this->model->create($fields);

        if ($result instanceof Collection) {
            $error = $this->responseGetError($result);
            if ($error !== null) {
                throw new \RuntimeException((string) $error);
            }

            $result = $result->toArray();
            $result = $this->toData($result);
        }

        if (! $result instanceof AirtableData) {
            throw new \RuntimeException('Failed to populate data');
        }

        return $result;
    }

    public function update(string|int $id, array $attributes, bool $checkExist = true): ?Data
    {
        $keyName = $this->model->getKeyName();

        if (array_key_exists($keyName, $attributes)) {
            unset($attributes[$keyName]);
        }

        if ($checkExist) {
            $isExist = $this->exist($id);

            if (! $isExist) {
                throw new \InvalidArgumentException('Record not found');
            }
        }

        $record = $this->model->update($id, $attributes);

        $error = $this->responseGetError($record);
        if ($error !== null) {
            throw new \RuntimeException((string) $error);
        }

        $record = $record->toArray();

        return $this->toData($record);
    }

    public function delete(string|int $id): bool
    {
        $isExist = $this->exist($id);

        if (! $isExist) {
            throw new \InvalidArgumentException('Record not found');
        }

        $result = $this->model->destroy($id);

        $error = $this->responseGetError($result);
        if ($error !== null) {
            throw new \RuntimeException((string) $error);
        }

        if ($result instanceof Collection) {
            $result = $result->toArray();
        }

        if (! isset($result['deleted'])) {
            throw new \RuntimeException('Failed to delete record');
        }

        return $result['deleted'];
    }
}
