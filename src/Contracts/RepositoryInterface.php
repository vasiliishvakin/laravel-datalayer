<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Contracts;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

interface RepositoryInterface
{
    public function dataClass(): string;

    public function emptyData(): Data;

    public function new(mixed $data): Data;

    public function find(string|int $id): ?Data;

    public function findOrFail(string|int $id): Data;

    /** @return Collection<int|string|int, Data> */
    public function all(): Collection;

    /** @return Data|null|Collection<string|int, Data> */
    public function findBy(string $field, mixed $value, bool $onlyFirst = false): Data|Collection|null;

    public function save(Data $data): Data;

    public function update(string|int $id, array $attributes): ?Data;

    public function delete(string|int $id): bool;
}
