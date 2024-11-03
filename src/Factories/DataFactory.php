<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Factories;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Vaskiq\LaravelDataLayer\Contracts\DataFactoryInterface;

class DataFactory implements DataFactoryInterface
{
    /**
     * @template TData of Data
     *
     * @param  class-string<TData>  $dataClass
     * @return TData
     */
    public static function create(mixed $source, string $dataClass): Data
    {
        return $dataClass::from($source);
    }

    /**
     * @template TData of Data
     *
     * @param  class-string<TData>  $dataClass
     */
    public static function empty(string $dataClass): array
    {
        return $dataClass::empty();
    }

    /**
     * @template TData of Data
     *
     * @param  Collection<int, mixed>  $collection
     * @param  class-string<TData>  $dataClass
     * @return Collection<int, TData>
     */
    public static function map(Collection $collection, string $dataClass): Collection
    {
        return $collection->map(fn ($model) => static::create($model, $dataClass));
    }
}
