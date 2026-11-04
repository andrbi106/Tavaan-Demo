<?php

declare(strict_types=1);

namespace Tavaan\Core\Exceptions;

class TavaanException extends \Exception
{
    public function __construct(
        private readonly string $messageId = 'tavaan.error',
        private readonly array $context = [],
        private readonly ?string $hint = null,
        ?string $message = null,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message ?? $hint ?? $messageId, $code, $previous);
    }

    public function messageId(): string { return $this->messageId; }
    public function context(): array { return $this->context; }
    public function hint(): ?string { return $this->hint; }

    public function toArray(): array
    {
        return [
            'message_id' => $this->messageId,
            'context' => $this->context,
            'hint' => $this->hint,
            'message' => $this->getMessage(),
        ];
    }
}
