<?php

namespace Vaskiq\LaravelDataLayer\Models;

use Airtable as AirtableFacade;
use Tapp\Airtable\Airtable;
use Vaskiq\LaravelDataLayer\Markers\AirtableModelInterface;

/**
 * @mixin \Tapp\Airtable\Airtable
 */
abstract class AirtableModel implements AirtableModelInterface
{
    protected string $table;

    protected string $primaryKey = 'recId';

    public function query(): Airtable
    {
        return AirtableFacade::table($this->table);
    }

    public function __call(string $method, array $arguments)
    {
        return $this->query()->$method(...$arguments);
    }

    public function getKeyName(): string
    {
        return $this->primaryKey;
    }
}
