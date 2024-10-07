<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Factories;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;
use Vaskiq\LaravelDataLayer\Contracts\DataFactoryInterface;

class DataFactory implements DataFactoryInterface
{
    public static function create(string $dataClass, mixed $source): Data
    {
        return $dataClass::from($source);
    }

    public static function empty(string $dataClass): Data
    {
        return $dataClass::empty();
    }

    public static function map(string $dataClass, Collection $collection): Collection
    {
        return $collection->map(fn ($model) => static::create($dataClass, $model));
    }
}
