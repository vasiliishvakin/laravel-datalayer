<?php

namespace Vaskiq\LaravelDataLayer\Data\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class JsonToArrayCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        return (is_string($value) && json_validate($value)) ? json_decode($value, true) ?? [] : [];
    }
}
