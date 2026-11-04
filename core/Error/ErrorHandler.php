<?php
declare(strict_types=1);
namespace Tavaan\Core\Error;

use Tavaan\Core\Contracts\Events\DispatcherInterface;

/**
 * Handler خطاهای PHP — تبدیل errors به exceptions و لاگ خطاها.
 */
final class ErrorHandler
{
    public function __construct(
        private readonly ?DispatcherInterface $events = null,
        private readonly ?LogMasker $masker = null,
    ) {}

    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleError(int $level, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $level)) {
            return false;
        }
        throw new \ErrorException($message, 0, $level, $file, $line);
    }

    public function handleException(\Throwable $e): void
    {
        $this->report($e);
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $this->report(new \ErrorException(
                $error['message'], 0, $error['type'], $error['file'], $error['line']
            ));
        }
    }

    private function report(\Throwable $e): void
    {
        if ($this->events !== null) {
            $this->events->dispatch('tavaan.error', ['exception' => $e]);
        }

        // لاگ به STDERR
        $msg = sprintf(
            "[%s] %s: %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            $e::class,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
        fwrite(STDERR, $msg);
    }
}
