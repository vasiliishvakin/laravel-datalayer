<?php

namespace Vaskiq\LaravelDataLayer\Errors;

readonly class AirtableError
{
    public function __construct(
        public string $message,
        public string $type = 'UNKNOWN',
    ) {}

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public function __toString(): string
    {
        return sprintf('AirtableError (%s):  %s', $this->type, $this->message);
    }
}
