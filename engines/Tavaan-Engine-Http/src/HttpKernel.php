<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http;

/**
 * HttpKernel — پردازش درخواست و تولید پاسخ.
 *
 * @see docs/03-engines/http.md
 */
final class HttpKernel
{
    /** @var list<callable(Request): void> */
    private array $middleware = [];

    /** @var callable(Request, Response): void|null */
    private $terminateCallback = null;

    private $dispatcher;

    public function __construct(
        ?callable $dispatcher = null,
    ) {
        $this->dispatcher = $dispatcher ?? fn(Request $r) => Response::text('Hello from Tavaan');
    }

    public function addMiddleware(callable $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function setDispatcher(callable $dispatcher): self
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    public function handle(Request $request): Response
    {
        // Run middleware (pre-processing)
        foreach ($this->middleware as $mw) {
            $result = $mw($request);
            if ($result instanceof Response) {
                return $result;
            }
        }
        // Dispatch to router/controller
        $dispatcher = $this->dispatcher;
        $response = $dispatcher($request);
        if ($response instanceof Response) return $response;
        if (\is_string($response)) return Response::text($response);
        if (\is_array($response)) return Response::json($response);
        return Response::text('Invalid response from dispatcher');
    }

    public function terminate(Request $request, Response $response): void
    {
        if ($this->terminateCallback !== null) {
            ($this->terminateCallback)($request, $response);
        }
    }

    public function setTerminate(callable $callback): self
    {
        $this->terminateCallback = $callback;
        return $this;
    }

    /**
     * Process request and send response (for CLI/server).
     */
    public function run(?Request $request = null): void
    {
        $request ??= Request::capture();
        $response = $this->handle($request);
        $response->send();
        $this->terminate($request, $response);
    }
}

/**
 * HttpServiceProvider.
 */
final class HttpServiceProvider
{
    private ?HttpKernel $kernel = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->kernel = new HttpKernel();
    }

    public function boot(): void {}

    public function provides(): array
    {
        return [HttpKernel::class, Request::class, Response::class];
    }

    public function getKernel(): HttpKernel
    {
        if ($this->kernel === null) $this->register();
        return $this->kernel;
    }

    /**
     * handleSubRequest
     */
    public function handleSubRequest(Request $request): Response
    {
        return $this->handle($request);
    }
    /**
     * sendAndTerminate
     */
    public function sendAndTerminate(Request $request): void
    {
        $response = $this->handle($request); $response->send(); $this->terminate($request, $response);
    }
}
