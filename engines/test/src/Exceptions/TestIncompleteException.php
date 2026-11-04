<?php

declare(strict_types=1);

namespace Tavaan\Testing\Exceptions;

use Tavaan\Core\Exceptions\TavaanException;

use RuntimeException;

/**
 * وقتی تست ناقص است (markTestIncomplete).
 */
final class TestIncompleteException extends TavaanException
{
}
