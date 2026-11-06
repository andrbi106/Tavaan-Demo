<?php
declare(strict_types=1);
namespace Tavaan\Core\Exceptions;

interface RenderableException
{
    public function render(mixed $request): mixed;
}
