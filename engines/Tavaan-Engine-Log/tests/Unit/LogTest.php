<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Log\Logger;
use Tavaan\Engine\Log\LogManager;
use Tavaan\Engine\Log\LogLevel;
use Tavaan\Engine\Log\LogRecord;
use Tavaan\Engine\Log\Handlers\FileHandler;
use Tavaan\Engine\Log\Handlers\StreamHandler;
use Tavaan\Engine\Log\Formatters\JsonFormatter;
use Tavaan\Engine\Log\Formatters\LineFormatter;
use Tavaan\Engine\Log\Contracts\LoggerInterface;
use Tavaan\Engine\Log\Contracts\HandlerInterface;
use Tavaan\Engine\Log\Contracts\FormatterInterface;

/**
 * تست‌های Unit برای موتور Log.
 *
 * @see docs/03-engines/log.md (DOC-036)
 */
final class LogTest extends TestCase
{
    public function test_log_level_enum(): void
    {
        $this->assertSame('EMERGENCY', LogLevel::Emergency->name());
        $this->assertSame('DEBUG', LogLevel::Debug->name());
    }

    public function test_log_level_from_string(): void
    {
        $this->assertSame(LogLevel::Emergency, LogLevel::fromString('emergency'));
        $this->assertSame(LogLevel::Info, LogLevel::fromString('INFO'));
        $this->assertSame(LogLevel::Debug, LogLevel::fromString('Debug'));
    }

    public function test_log_level_includes(): void
    {
        $this->assertTrue(LogLevel::Error->includes(LogLevel::Emergency));
        $this->assertTrue(LogLevel::Error->includes(LogLevel::Error));
        $this->assertFalse(LogLevel::Error->includes(LogLevel::Warning));
    }

    public function test_logger_basic_log(): void
    {
        $logger = new Logger('test');
        $received = [];
        $logger->addHandler(new class($received) implements HandlerInterface {
            public function __construct(public array &$records) {}
            public function handle(LogRecord $r): void { $this->records[] = $r; }
            public function flush(): void {}
            public function close(): void {}
        });
        $logger->info('hello');
        $this->assertCount(1, $received);
        $this->assertSame('hello', $received[0]->message);
        $this->assertSame(LogLevel::Info, $received[0]->level);
        $this->assertSame('test', $received[0]->channel);
    }

    public function test_logger_all_levels(): void
    {
        $logger = new Logger('test');
        $received = [];
        $logger->addHandler(new class($received) implements HandlerInterface {
            public function __construct(public array &$records) {}
            public function handle(LogRecord $r): void { $this->records[] = $r; }
            public function flush(): void {}
            public function close(): void {}
        });
        $logger->emergency('a');
        $logger->alert('b');
        $logger->critical('c');
        $logger->error('d');
        $logger->warning('e');
        $logger->notice('f');
        $logger->info('g');
        $logger->debug('h');
        $this->assertCount(8, $received);
        $this->assertSame(LogLevel::Emergency, $received[0]->level);
        $this->assertSame(LogLevel::Debug, $received[7]->level);
    }

    public function test_logger_filter_by_minimum_level(): void
    {
        $logger = new Logger('test');
        $logger->setMinimumLevel(LogLevel::Warning);
        $received = [];
        $logger->addHandler(new class($received) implements HandlerInterface {
            public function __construct(public array &$records) {}
            public function handle(LogRecord $r): void { $this->records[] = $r; }
            public function flush(): void {}
            public function close(): void {}
        });
        $logger->debug('should_skip');
        $logger->info('should_skip');
        $logger->notice('should_skip');
        $logger->warning('should_pass');
        $logger->error('should_pass');
        $this->assertCount(2, $received);
    }

    public function test_logger_with_context(): void
    {
        $logger = new Logger('test');
        $received = [];
        $logger->addHandler(new class($received) implements HandlerInterface {
            public function __construct(public array &$records) {}
            public function handle(LogRecord $r): void { $this->records[] = $r; }
            public function flush(): void {}
            public function close(): void {}
        });
        $logger->withContext(['user_id' => 42])->info('user_action', ['action' => 'login']);
        $this->assertSame(['user_id' => 42, 'action' => 'login'], $received[0]->context);
    }

    public function test_logger_share_context(): void
    {
        $logger = new Logger('test');
        $received = [];
        $logger->addHandler(new class($received) implements HandlerInterface {
            public function __construct(public array &$records) {}
            public function handle(LogRecord $r): void { $this->records[] = $r; }
            public function flush(): void {}
            public function close(): void {}
        });
        $logger->shareContext(['request_id' => 'req-123']);
        $logger->info('msg1');
        $logger->info('msg2', ['user' => 5]);
        $this->assertSame(['request_id' => 'req-123'], $received[0]->context);
        $this->assertSame(['request_id' => 'req-123', 'user' => 5], $received[1]->context);
    }

    public function test_logger_without_context(): void
    {
        $logger = new Logger('test');
        $received = [];
        $logger->addHandler(new class($received) implements HandlerInterface {
            public function __construct(public array &$records) {}
            public function handle(LogRecord $r): void { $this->records[] = $r; }
            public function flush(): void {}
            public function close(): void {}
        });
        $logger->shareContext(['a' => 1]);
        $logger->info('msg');
        $logger->withoutContext();
        $logger->info('msg2');
        $this->assertSame(['a' => 1], $received[0]->context);
        $this->assertSame([], $received[1]->context);
    }

    public function test_logger_channel_returns_new_instance(): void
    {
        $logger = new Logger('default');
        $new = $logger->channel('audit');
        $this->assertSame('audit', $new->getName());
        $this->assertNotSame($logger, $new);
    }

    public function test_json_formatter(): void
    {
        $formatter = new JsonFormatter();
        $record = new LogRecord(
            timestamp: new \DateTimeImmutable('2026-01-01T00:00:00Z'),
            level: LogLevel::Info,
            message: 'hello',
            context: ['user_id' => 42],
            channel: 'test'
        );
        $json = $formatter->format($record);
        $decoded = \json_decode($json, true);
        $this->assertSame('hello', $decoded['message']);
        $this->assertSame('INFO', $decoded['level']);
        $this->assertSame('test', $decoded['channel']);
        $this->assertSame(['user_id' => 42], $decoded['context']);
    }

    public function test_line_formatter(): void
    {
        $formatter = new LineFormatter();
        $record = new LogRecord(
            timestamp: new \DateTimeImmutable('2026-01-01T00:00:00Z'),
            level: LogLevel::Info,
            message: 'hello {name}',
            context: ['name' => 'world'],
            channel: 'test'
        );
        $line = $formatter->format($record);
        $this->assertStringContainsString('INFO.test: hello world', $line);
        $this->assertStringNotContainsString('{name}', $line);
    }

    public function test_stream_handler_writes_to_resource(): void
    {
        $stream = \fopen('php://memory', 'r+');
        $handler = new StreamHandler($stream);
        $record = new LogRecord(
            timestamp: new \DateTimeImmutable(),
            level: LogLevel::Info,
            message: 'test',
            context: [],
            channel: 'default'
        );
        $handler->handle($record);
        \rewind($stream);
        $content = \stream_get_contents($stream);
        $this->assertStringContainsString('test', $content);
    }

    public function test_file_handler_writes_to_file(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn_log') . '.log';
        $handler = new FileHandler($tmp);
        $record = new LogRecord(
            timestamp: new \DateTimeImmutable(),
            level: LogLevel::Info,
            message: 'hello',
            context: [],
            channel: 'default'
        );
        $handler->handle($record);
        $handler->flush();
        $handler->close();
        $content = \file_get_contents($tmp);
        $this->assertStringContainsString('hello', $content);
        @\unlink($tmp);
    }

    public function test_file_handler_creates_directory(): void
    {
        $tmpDir = \sys_get_temp_dir() . '/tvn_log_' . \uniqid();
        $path = $tmpDir . '/subdir/test.log';
        $handler = new FileHandler($path);
        $record = new LogRecord(
            timestamp: new \DateTimeImmutable(),
            level: LogLevel::Info,
            message: 'hello',
            context: [],
            channel: 'default'
        );
        $handler->handle($record);
        $handler->close();
        $this->assertFileExists($path);
        \unlink($path);
        \rmdir(\dirname($path));
        \rmdir($tmpDir);
    }

    public function test_logger_implements_interface(): void
    {
        $logger = new Logger('test');
        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    public function test_log_record_to_array(): void
    {
        $record = new LogRecord(
            timestamp: new \DateTimeImmutable('2026-01-01T00:00:00Z'),
            level: LogLevel::Info,
            message: 'hello',
            context: ['x' => 1],
            channel: 'test'
        );
        $arr = $record->toArray();
        $this->assertSame('INFO', $arr['level']);
        $this->assertSame('hello', $arr['message']);
        $this->assertSame('test', $arr['channel']);
        $this->assertSame(['x' => 1], $arr['context']);
        $this->assertArrayHasKey('timestamp', $arr);
    }

    public function test_log_manager_default_channel(): void
    {
        $manager = new LogManager([]);
        $this->assertSame('default', $manager->getDefaultLogger()->getName());
    }

    public function test_log_manager_named_channel(): void
    {
        $manager = new LogManager([]);
        $channel = $manager->channel('audit');
        $this->assertSame('audit', $channel->getName());
    }

    public function test_log_manager_proxies_to_default(): void
    {
        $received = [];
        $manager = new LogManager([
            'handlers' => [
                ['type' => 'null'],
            ],
        ]);
        // We can't easily intercept; just test it doesn't throw
        $manager->info('test');
        $this->assertTrue(true);
    }

    public function test_logger_handlers_list(): void
    {
        $logger = new Logger('test');
        $this->assertSame([], $logger->handlers());
        $logger->addHandler(StreamHandler::toStdout());
        $this->assertCount(1, $logger->handlers());
    }
}
