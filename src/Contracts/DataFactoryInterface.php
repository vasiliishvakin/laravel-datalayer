<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Contracts;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

interface DataFactoryInterface
{
    public static function create(string $dataClass, mixed $source): Data;

    public static function empty(string $dataClass): Data;

    public static function map(string $dataClass, Collection $collection): Collection;
}
