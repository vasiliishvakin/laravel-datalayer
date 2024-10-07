<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Repositories;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Vaskiq\LaravelDataLayer\Contracts\DataFactoryInterface;
use Vaskiq\LaravelDataLayer\Contracts\RepositoryInterface;
use Vaskiq\LaravelDataLayer\Exceptions\RepositoryNotFoundException;

abstract class AbstractRepository implements RepositoryInterface
{
    protected string $dataClass;

    public function __construct(
        protected readonly DataFactoryInterface $dataFactory,
    ) {}

    public function dataClass(): string
    {
        return $this->dataClass;
    }

    abstract public function find(string|int $id): ?Data;

    protected function toData(mixed $source): Data
    {
        return $this->dataFactory->create($this->dataClass(), $source);
    }

    public function emptyData(): Data
    {
        return $this->dataFactory->empty($this->dataClass());
    }

    protected function toDataCollection(Collection $models): Collection
    {
        return $this->dataFactory->map($this->dataClass(), $models);
    }

    public function new(mixed $data): Data
    {
        return $this->toData($data);
    }

    public function findOrFail(string|int $id): Data
    {
        $data = $this->find($id);
        if (! $data) {
            throw new RepositoryNotFoundException($id, get_class($this));
        }

        return $data;
    }
}
