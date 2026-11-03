<?php

declare(strict_types=1);

namespace Tavaan\Engine\Error;

use Tavaan\Engine\Error\Renderers\JsonRenderer;
use Tavaan\Engine\Error\Renderers\HtmlRenderer;

/**
 * ErrorHandler — مدیریت خطا و استثنا.
 *
 * @see docs/03-engines/error.md (DOC-035)
 */
final class ErrorHandler
{
    private bool $registered = false;
    private bool $debug = false;
    private LogMasker $masker;
    private ExceptionMapper $mapper;

    public function __construct(
        bool $debug = false,
        private readonly ?object $logger = null,
    ) {
        $this->debug = $debug;
        $this->masker = new LogMasker();
        $this->mapper = new ExceptionMapper();
    }

    /**
     * ثبت error handler, exception handler و shutdown handler.
     */
    public function register(): self
    {
        if ($this->registered) return $this;
        \set_error_handler(\Closure::fromCallable([$this, 'handleError']));
        \set_exception_handler(\Closure::fromCallable([$this, 'handleException']));
        \register_shutdown_function(\Closure::fromCallable([$this, 'handleShutdown']));
        $this->registered = true;
        return $this;
    }

    public function unregister(): self
    {
        if (!$this->registered) return $this;
        \restore_error_handler();
        \restore_exception_handler();
        $this->registered = false;
        return $this;
    }

    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @param list<string> $keys
     */
    public function addSensitiveKeys(string ...$keys): self
    {
        $this->masker->addSensitiveKey(...$keys);
        return $this;
    }

    /**
     * @internal
     */
    public function handleError(int $severity, string $message, string $file, int $line): bool
    {
        if (!(\error_reporting() & $severity)) return false;
        throw new \ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * @internal
     */
    public function handleException(\Throwable $e): void
    {
        $this->report($e);
        $this->render($e);
    }

    public function report(\Throwable $e): void
    {
        if ($this->logger === null) return;
        $httpException = $this->mapper->map($e);
        $context = [
            'code' => $httpException->errorCode,
            'ref' => $httpException->refCode ?? RefCodeGenerator::generate(),
            'status' => $httpException->statusCode,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'context' => $this->masker->mask($httpException->context),
        ];
        // Assume logger has log() method
        if (\method_exists($this->logger, 'error')) {
            $this->logger->error($e->getMessage(), $context);
        }
    }

    public function render(\Throwable $e): void
    {
        $httpException = $this->mapper->map($e);
        if ($httpException->refCode === null) {
            $httpException = new HttpException(
                $httpException->statusCode,
                $httpException->getMessage(),
                $httpException->errorCode,
                $httpException->context,
                $e->getPrevious(),
                RefCodeGenerator::generate(),
            );
        }
        // Detect format from SAPI or accept negotiation
        if (\PHP_SAPI === 'cli') {
            echo "Error [{$httpException->statusCode}] {$httpException->errorCode}: {$httpException->getMessage()} (ref: {$httpException->refCode})
";
            return;
        }
        $isJson = \str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
        if ($isJson) {
            $renderer = new JsonRenderer();
            \http_response_code($httpException->statusCode);
            \header('Content-Type: application/problem+json; charset=utf-8');
            echo $renderer->render($httpException, $this->debug);
        } else {
            $renderer = new HtmlRenderer();
            \http_response_code($httpException->statusCode);
            \header('Content-Type: text/html; charset=utf-8');
            echo $renderer->render($httpException, $this->debug);
        }
    }

    /**
     * @internal
     */
    public function handleShutdown(): void
    {
        $error = \error_get_last();
        if ($error === null) return;
        $e = new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
        $this->handleException($e);
    }

    public function getMapper(): ExceptionMapper
    {
        return $this->mapper;
    }

    public function getMasker(): LogMasker
    {
        return $this->masker;
    }
}
