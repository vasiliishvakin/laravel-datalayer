<?php

namespace Vaskiq\LaravelDataLayer\Data;

use Spatie\LaravelData\Data;

abstract class AirtableData extends Data
{
    protected static array $mapFields = [];

    protected array $hiddenFields = [
        'createdTime',
    ];

    protected static array $reversedMapFields;

    private static array $mapsCache = [];

    public static function mapFields(): array
    {
        return static::$mapFields;
    }

    public static function reversedMapFields(): array
    {
        if (! isset(static::$reversedMapFields)) {
            static::$reversedMapFields = array_flip(static::$mapFields);
        }

        return static::$reversedMapFields;
    }

    public static function mapField(string $field): string
    {
        if (! isset(static::$mapsCache[$field])) {
            $mapped = static::mapFields()[$field] ?? static::reversedMapFields()[$field] ?? $field;
            static::$mapsCache[$field] = $mapped;
        }

        return static::$mapsCache[$field];
    }

    protected static function field(string $name, array $data, ?callable $closure = null): mixed
    {
        $fieldName = static::mapField($name);

        $result = match ($fieldName) {
            'recId' => $data['id'],
            'createdTime' => $data['createdTime'],
            default => $data['fields'][$fieldName] ?? null,
        };

        if (is_callable($closure)) {
            $result = $closure($result);
        }

        return $result;
    }

    public function toAirtableArray(?callable $closure = null): array
    {
        $data = $this->toArray();
        $fields = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $this->hiddenFields)) {
                continue;
            }

            $fieldAlias = static::mapField($key);
            if (is_callable($closure)) {
                [$fieldAlias, $value] = $closure($fieldAlias, $value);
            }
            $fields[$fieldAlias] = $value;
        }

        return $fields;
    }
}
