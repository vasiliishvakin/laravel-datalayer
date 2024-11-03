<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Contracts;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

/**
 * @template TData of Data
 */
interface RepositoryInterface
{
    /**
     * @return class-string<TData>
     */
    public function dataClass(): string;

    public function empty(): array;

    /**
     * @return TData
     */
    public function new(mixed $data): Data;

    /**
     * @return TData|null
     */
    public function find(string|int $id): ?Data;

    /**
     * @return TData
     */
    public function findOrFail(string|int $id): Data;

    /**
     * @return Collection<int|string, TData>
     */
    public function all(): Collection;

    /**
     * @return TData|null|Collection<int|string, TData>
     */
    public function findBy(string $field, mixed $value, bool $onlyFirst = false): Data|Collection|null;

    /**
     * @param  TData  $data
     * @return TData
     */
    public function save(Data $data): Data;

    /**
     * @param  array<string, mixed>  $attributes
     * @return TData|null
     */
    public function update(string|int $id, array $attributes): ?Data;

    public function delete(string|int $id): bool;
}
