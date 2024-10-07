<?php

declare(strict_types=1);

namespace Vaskiq\LaravelDataLayer\Exceptions;

use Exception;

class RepositoryNotFoundException extends Exception
{
    public function __construct(string|int $identifier, ?string $repositoryName = null)
    {
        $message = "Repository not found for identifier: {$identifier}";
        if ($repositoryName) {
            $message .= " and repository name: {$repositoryName}";
        }
        parent::__construct($message);
    }
}
