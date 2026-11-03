<?php

declare(strict_types=1);

namespace Tavaan\Testing\Exceptions;

use RuntimeException;

/**
 * وقتی تست ناقص است (markTestIncomplete).
 */
final class TestIncompleteException extends RuntimeException
{
}
