<?php
declare(strict_types=1);
namespace Tavaan\Core\Exceptions;

class ContextualException extends TavaanException
{
    public function withContext(array $context): static
    {
        return new static($this->messageId(), array_merge($this->context(), $context), $this->hint(), $this->getMessage(), $this->getCode(), $this->getPrevious());
    }
}
