<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Contracts;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

interface DataFactoryInterface
{
    /**
     * @template TData of Data
     *
     * @param  class-string<TData>  $dataClass
     * @return TData
     */
    public static function create(mixed $source, string $dataClass): Data;

    /**
     * @template TData of Data
     *
     * @param  class-string<TData>  $dataClass
     */
    public static function empty(string $dataClass): array;

    /**
     * @template TData of Data
     *
     * @param  Collection<int, mixed>  $collection
     * @param  class-string<TData>  $dataClass
     * @return Collection<int, TData>
     */
    public static function map(Collection $collection, string $dataClass): Collection;
}
