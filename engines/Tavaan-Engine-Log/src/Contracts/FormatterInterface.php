<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log\Contracts;

use Tavaan\Engine\Log\LogRecord;

/**
 * قرارداد Formatter — نحوه تبدیل LogRecord به رشته.
 */
interface FormatterInterface
{
    public function format(LogRecord $record): string;
}
