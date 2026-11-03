<?php

declare(strict_types=1);

namespace Tavaan\Engine\Validation\Exceptions;

use RuntimeException;

final class ValidationException extends RuntimeException
{
    /**
     * @param array<string, list<string>> $errors
     */
    public function __construct(
        public readonly array $errors = [],
        string $message = 'The given data was invalid.',
    ) {
        parent::__construct($message);
    }

    /**
     * @return list<string>
     */
    public function all(): array
    {
        $all = [];
        foreach ($this->errors as $errors) {
            foreach ($errors as $e) $all[] = $e;
        }
        return $all;
    }
}
