<?php
declare(strict_types=1);
namespace Tavaan\Core\Exceptions;

class RetryableException extends ContextualException
{
    public function __construct(
        string $messageId = 'tavaan.retryable',
        array $context = [],
        ?string $hint = null,
        private readonly int $maxRetries = 3,
        private readonly int $retryDelayMs = 100,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($messageId, $context, $hint, null, 0, $previous);
    }
    public function maxRetries(): int { return $this->maxRetries; }
    public function retryDelayMs(): int { return $this->retryDelayMs; }
}
