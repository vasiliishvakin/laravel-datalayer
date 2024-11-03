<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Repositories;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Vaskiq\LaravelDataLayer\Contracts\DataFactoryInterface;
use Vaskiq\LaravelDataLayer\Contracts\RepositoryInterface;
use Vaskiq\LaravelDataLayer\Exceptions\RepositoryNotFoundException;

/**
 * @template TData of Data
 * @template TModel
 *
 * @implements RepositoryInterface<TData>
 */
abstract class AbstractRepository implements RepositoryInterface
{
    public function __construct(
        protected readonly DataFactoryInterface $dataFactory,
    ) {}

    /**
     * @return class-string<TData>
     */
    abstract public function dataClass(): string;

    /**
     * @return TData|null
     */
    abstract public function find(string|int $id): ?Data;

    /**
     * @return array<string, mixed>
     */
    public function empty(): array
    {
        return $this->dataFactory->empty($this->dataClass());
    }

    /**
     * @param  TModel  $data
     * @return TData
     */
    public function new(mixed $data): Data
    {
        return $this->toData($data);
    }

    /**
     * @return TData
     *
     * @throws RepositoryNotFoundException
     */
    public function findOrFail(string|int $id): Data
    {
        $data = $this->find($id);
        if (! $data) {
            throw new RepositoryNotFoundException($id, get_class($this));
        }

        return $data;
    }

    /**
     * @param  TModel  $source
     * @return TData
     */
    protected function toData(mixed $source): Data
    {
        return $this->dataFactory->create($source, $this->dataClass());
    }

    /**
     * @param  Collection<int, TModel>  $models
     * @return Collection<int, TData>
     */
    protected function toDataCollection(Collection $models): Collection
    {
        return $this->dataFactory->map($models, $this->dataClass());
    }
}
