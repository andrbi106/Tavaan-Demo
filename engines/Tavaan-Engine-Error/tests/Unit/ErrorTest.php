<?php

declare(strict_types=1);

namespace Tavaan\Engine\Error\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Error\HttpException;
use Tavaan\Engine\Error\ErrorCode;
use Tavaan\Engine\Error\ExceptionMapper;
use Tavaan\Engine\Error\LogMasker;
use Tavaan\Engine\Error\RefCodeGenerator;
use Tavaan\Engine\Error\ErrorHandler;
use Tavaan\Engine\Error\Renderers\JsonRenderer;
use Tavaan\Engine\Error\Renderers\HtmlRenderer;

/**
 * تست‌های Unit برای موتور Error.
 *
 * @see docs/03-engines/error.md (DOC-035)
 */
final class ErrorTest extends TestCase
{
    public function test_error_code_constants(): void
    {
        $this->assertSame('ERR.NOT_FOUND', ErrorCode::NOT_FOUND);
        $this->assertSame('ERR.UNAUTHORIZED', ErrorCode::UNAUTHORIZED);
        $this->assertSame('ERR.VALIDATION', ErrorCode::VALIDATION);
    }

    public function test_http_exception_not_found(): void
    {
        $e = HttpException::notFound('Page not found');
        $this->assertSame(404, $e->statusCode);
        $this->assertSame('Page not found', $e->getMessage());
        $this->assertSame(ErrorCode::NOT_FOUND, $e->errorCode);
    }

    public function test_http_exception_unauthorized(): void
    {
        $e = HttpException::unauthorized();
        $this->assertSame(401, $e->statusCode);
        $this->assertSame(ErrorCode::UNAUTHORIZED, $e->errorCode);
    }

    public function test_http_exception_forbidden(): void
    {
        $e = HttpException::forbidden();
        $this->assertSame(403, $e->statusCode);
        $this->assertSame(ErrorCode::FORBIDDEN, $e->errorCode);
    }

    public function test_http_exception_bad_request(): void
    {
        $e = HttpException::badRequest();
        $this->assertSame(400, $e->statusCode);
    }

    public function test_http_exception_validation(): void
    {
        $e = HttpException::validation(['name' => 'required']);
        $this->assertSame(422, $e->statusCode);
        $this->assertSame(['errors' => ['name' => 'required']], $e->context);
    }

    public function test_http_exception_server_error(): void
    {
        $e = HttpException::serverError('db failed');
        $this->assertSame(500, $e->statusCode);
    }

    public function test_http_exception_rate_limited(): void
    {
        $e = HttpException::rateLimited(retryAfter: 120);
        $this->assertSame(429, $e->statusCode);
        $this->assertSame(120, $e->context['retry_after']);
    }

    public function test_http_exception_maintenance(): void
    {
        $e = HttpException::maintenance();
        $this->assertSame(503, $e->statusCode);
    }

    public function test_http_exception_with_ref_code(): void
    {
        $e = new HttpException(404, 'msg', ErrorCode::NOT_FOUND, [], null, 'ABC12345');
        $this->assertSame('ABC12345', $e->refCode);
    }

    public function test_exception_mapper_returns_same_http_exception(): void
    {
        $e = HttpException::notFound();
        $mapper = new ExceptionMapper();
        $this->assertSame($e, $mapper->map($e));
    }

    public function test_exception_mapper_invalid_argument(): void
    {
        $mapper = new ExceptionMapper();
        $e = new \InvalidArgumentException('Bad input');
        $mapped = $mapper->map($e);
        $this->assertSame(400, $mapped->statusCode);
        $this->assertSame('Bad input', $mapped->getMessage());
    }

    public function test_exception_mapper_runtime_exception(): void
    {
        $mapper = new ExceptionMapper();
        $e = new \RuntimeException('System failure');
        $mapped = $mapper->map($e);
        $this->assertSame(500, $mapped->statusCode);
    }

    public function test_exception_mapper_unknown(): void
    {
        $mapper = new ExceptionMapper();
        $e = new \Exception('Generic');
        $mapped = $mapper->map($e);
        $this->assertSame(500, $mapped->statusCode);
    }

    public function test_ref_code_generator_returns_unique(): void
    {
        $code1 = RefCodeGenerator::generate();
        $code2 = RefCodeGenerator::generate();
        $this->assertSame(8, \strlen($code1));
        $this->assertSame(8, \strlen($code2));
        $this->assertNotSame($code1, $code2);
    }

    public function test_log_masker_basic(): void
    {
        $m = new LogMasker();
        $masked = $m->mask([
            'username' => 'ali',
            'password' => 'secret123',
            'token' => 'xyz',
        ]);
        $this->assertSame('ali', $masked['username']);
        $this->assertSame('***masked***', $masked['password']);
        $this->assertSame('***masked***', $masked['token']);
    }

    public function test_log_masker_case_insensitive(): void
    {
        $m = new LogMasker();
        $masked = $m->mask(['PASSWORD' => 'x', 'Api_Key' => 'y']);
        $this->assertSame('***masked***', $masked['PASSWORD']);
        $this->assertSame('***masked***', $masked['Api_Key']);
    }

    public function test_log_masker_recursive(): void
    {
        $m = new LogMasker();
        $masked = $m->mask(['user' => ['name' => 'ali', 'password' => 'x']]);
        $this->assertSame('ali', $masked['user']['name']);
        $this->assertSame('***masked***', $masked['user']['password']);
    }

    public function test_log_masker_partial_match(): void
    {
        $m = new LogMasker();
        $masked = $m->mask(['user_password' => 'x', 'password_confirmation' => 'y']);
        $this->assertSame('***masked***', $masked['user_password']);
        $this->assertSame('***masked***', $masked['password_confirmation']);
    }

    public function test_log_masker_add_custom_key(): void
    {
        $m = new LogMasker();
        $m->addSensitiveKey('custom_secret');
        $masked = $m->mask(['custom_secret' => 'x']);
        $this->assertSame('***masked***', $masked['custom_secret']);
    }

    public function test_json_renderer(): void
    {
        $e = HttpException::notFound('Page not found');
        $e = new HttpException(404, 'Page not found', ErrorCode::NOT_FOUND, [], null, 'ABC12345');
        $renderer = new JsonRenderer();
        $json = $renderer->render($e);
        $decoded = \json_decode($json, true);
        $this->assertSame(404, $decoded['status']);
        $this->assertSame('Page not found', $decoded['detail']);
        $this->assertSame('ABC12345', $decoded['ref']);
        $this->assertSame('ERR.NOT_FOUND', $decoded['code']);
    }

    public function test_json_renderer_with_context(): void
    {
        $e = new HttpException(400, 'Bad', ErrorCode::BAD_REQUEST, ['user_id' => 42]);
        $renderer = new JsonRenderer();
        $json = $renderer->render($e);
        $decoded = \json_decode($json, true);
        $this->assertSame(['user_id' => 42], $decoded['context']);
    }

    public function test_html_renderer(): void
    {
        $e = new HttpException(404, 'Not found', ErrorCode::NOT_FOUND);
        $renderer = new HtmlRenderer();
        $html = $renderer->render($e);
        $this->assertStringContainsString('<html', $html);
        $this->assertStringContainsString('Not found', $html);
        $this->assertStringContainsString('404', $html);
    }

    public function test_html_renderer_escapes_html(): void
    {
        $e = new HttpException(400, '<script>alert(1)</script>', ErrorCode::BAD_REQUEST);
        $renderer = new HtmlRenderer();
        $html = $renderer->render($e);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_error_handler_construct(): void
    {
        $h = new ErrorHandler(debug: false);
        $this->assertFalse($h->isDebug());
        $h->setDebug(true);
        $this->assertTrue($h->isDebug());
    }

    public function test_error_handler_add_sensitive_keys(): void
    {
        $h = new ErrorHandler();
        $h->addSensitiveKeys('custom_key');
        $this->assertTrue(true);  // just verify no exception
    }

    public function test_error_handler_get_mapper(): void
    {
        $h = new ErrorHandler();
        $this->assertInstanceOf(ExceptionMapper::class, $h->getMapper());
    }

    public function test_error_handler_get_masker(): void
    {
        $h = new ErrorHandler();
        $this->assertInstanceOf(LogMasker::class, $h->getMasker());
    }

    public function test_error_handler_report_with_logger(): void
    {
        $logCalls = [];
        $logger = new class($logCalls) {
            public array $calls;
            public function __construct(array &$calls) { $this->calls = &$calls; }
            public function error(string $message, array $context = []): void {
                $this->calls[] = ['message' => $message, 'context' => $context];
            }
        };
        $h = new ErrorHandler(logger: $logger);
        $e = new \RuntimeException('test error');
        $h->report($e);
        $this->assertCount(1, $logger->calls);
        $this->assertSame('test error', $logger->calls[0]['message']);
    }

    public function test_error_handler_report_without_logger(): void
    {
        $h = new ErrorHandler();
        $h->report(new \RuntimeException('test'));
        $this->assertTrue(true);  // no exception
    }

    public function test_http_exception_extends_runtime_exception(): void
    {
        $e = HttpException::serverError();
        $this->assertInstanceOf(\RuntimeException::class, $e);
    }
}
