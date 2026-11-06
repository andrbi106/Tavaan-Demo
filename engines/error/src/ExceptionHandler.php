<?php

declare(strict_types=1);

namespace Tavaan\Engine\Error;

final class ExceptionHandler
{
    private array $dontReportList = [];
    private array $renderables = [];
    private array $reportables = [];
    private array $context = [];
    private array $levels = [];

    public function __construct(
        private readonly ?object $logger = null,
        private bool $debug = false,
    ) {
    }

    public function report(\Throwable $e): void
    {
        foreach ($this->dontReportList as $class) {
            if ($e instanceof $class) return;
        }
        if ($this->logger !== null && method_exists($this->logger, 'error')) {
            $this->logger->error($e->getMessage(), $this->buildContext($e));
        }
    }

    public function render(\Throwable $e): string
    {
        foreach ($this->renderables as $renderable) {
            $result = $renderable($e, $this->debug);
            if ($result !== null) return $result;
        }
        $status = $e instanceof HttpException ? $e->statusCode : 500;
        $message = $e->getMessage();
        if (!$this->debug && $status >= 500) {
            $message = 'Internal Server Error';
        }
        $renderer = new Renderers\JsonRenderer();
        $httpException = $e instanceof HttpException ? $e : HttpException::serverError($message);
        return $renderer->render($httpException, $this->debug);
    }

    public function map(string $from, string|callable $to): self
    {
        $this->map[$from] = $to;
        return $this;
    }

    public function renderable(callable $callback): self
    {
        $this->renderables[] = $callback;
        return $this;
    }

    public function reportable(callable $callback): self
    {
        $this->reportables[] = $callback;
        return $this;
    }

    public function dontReport(string ...$classes): self
    {
        foreach ($classes as $class) {
            $this->dontReportList[] = $class;
        }
        return $this;
    }

    public function level(string $class, string $level): self
    {
        $this->levels[$class] = $level;
        return $this;
    }

    public function context(array $context): self
    {
        $this->context = array_merge($this->context, $context);
        return $this;
    }

    private function buildContext(\Throwable $e): array
    {
        return array_merge($this->context, [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    }

    private array $map = [];
}
