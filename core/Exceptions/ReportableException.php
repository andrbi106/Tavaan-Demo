<?php
declare(strict_types=1);
namespace Tavaan\Core\Exceptions;

interface ReportableException
{
    public function report(): ?bool;
}
