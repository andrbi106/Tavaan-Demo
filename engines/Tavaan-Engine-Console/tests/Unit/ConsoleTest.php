<?php

declare(strict_types=1);

namespace Tavaan\Engine\Console\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Console\Application;
use Tavaan\Engine\Console\ArgvParser;
use Tavaan\Engine\Console\SignatureParser;
use Tavaan\Engine\Console\Command;
use Tavaan\Engine\Console\ListCommand;

/**
 * تست‌های Unit برای موتور Console.
 *
 * @see docs/03-engines/console.md (DOC-076)
 */
final class ConsoleTest extends TestCase
{
    public function test_argv_parser_simple_arg(): void
    {
        [$args, $opts] = ArgvParser::parse(['value1', 'value2']);
        $this->assertSame(['value1', 'value2'], $args);
        $this->assertSame([], $opts);
    }

    public function test_argv_parser_long_option_with_value(): void
    {
        [$args, $opts] = ArgvParser::parse(['--name=value']);
        $this->assertSame([], $args);
        $this->assertSame(['name' => 'value'], $opts);
    }

    public function test_argv_parser_long_option_separate_value(): void
    {
        [$args, $opts] = ArgvParser::parse(['--name', 'value']);
        $this->assertSame([], $args);
        $this->assertSame(['name' => 'value'], $opts);
    }

    public function test_argv_parser_long_flag(): void
    {
        [$args, $opts] = ArgvParser::parse(['--force', 'value']);
        $this->assertSame(['value'], $args);
        $this->assertSame(['force' => true], $opts);
    }

    public function test_argv_parser_short_option(): void
    {
        [$args, $opts] = ArgvParser::parse(['-f', 'value']);
        $this->assertSame([], $args);
        $this->assertSame(['f' => 'value'], $opts);
    }

    public function test_argv_parser_short_flag(): void
    {
        [$args, $opts] = ArgvParser::parse(['-v', 'value']);
        $this->assertSame(['value'], $args);
        $this->assertSame(['v' => true], $opts);
    }

    public function test_argv_parser_mixed(): void
    {
        [$args, $opts] = ArgvParser::parse(['command', 'arg1', '--flag', 'value', '-x']);
        $this->assertSame(['command', 'arg1', 'value'], $args);
        $this->assertSame(['flag' => true, 'x' => true], $opts);
    }

    public function test_argv_parser_empty(): void
    {
        [$args, $opts] = ArgvParser::parse([]);
        $this->assertSame([], $args);
        $this->assertSame([], $opts);
    }

    public function test_signature_parser_name_only(): void
    {
        $result = SignatureParser::parse('command:name');
        $this->assertSame('command:name', $result['name']);
        $this->assertSame([], $result['arguments']);
        $this->assertSame([], $result['options']);
    }

    public function test_signature_parser_required_argument(): void
    {
        $result = SignatureParser::parse('cmd {arg}');
        $this->assertCount(1, $result['arguments']);
        $this->assertSame('arg', $result['arguments'][0]['name']);
        $this->assertTrue($result['arguments'][0]['required']);
    }

    public function test_signature_parser_optional_argument(): void
    {
        $result = SignatureParser::parse('cmd {arg?}');
        $this->assertFalse($result['arguments'][0]['required']);
    }

    public function test_signature_parser_argument_with_description(): void
    {
        $result = SignatureParser::parse('cmd {arg : description here}');
        $this->assertSame('arg', $result['arguments'][0]['name']);
        $this->assertSame('description here', $result['arguments'][0]['description']);
    }

    public function test_signature_parser_flag_option(): void
    {
        $result = SignatureParser::parse('cmd {--flag}');
        $this->assertCount(1, $result['options']);
        $this->assertSame('flag', $result['options'][0]['name']);
        $this->assertFalse($result['options'][0]['has_value']);
    }

    public function test_signature_parser_option_with_default(): void
    {
        $result = SignatureParser::parse('cmd {--opt=default}');
        $this->assertSame('opt', $result['options'][0]['name']);
        $this->assertTrue($result['options'][0]['has_value']);
        $this->assertSame('default', $result['options'][0]['default']);
    }

    public function test_signature_parser_full_signature(): void
    {
        $result = SignatureParser::parse('cache:clear {store? : نام store} {--tags= : filter by tags} {--force : skip confirm}');
        $this->assertSame('cache:clear', $result['name']);
        $this->assertCount(1, $result['arguments']);
        $this->assertCount(2, $result['options']);
        $this->assertSame('store', $result['arguments'][0]['name']);
        $this->assertFalse($result['arguments'][0]['required']);
        $this->assertSame('force', $result['options'][1]['name']);
        $this->assertFalse($result['options'][1]['has_value']);
    }

    public function test_application_add_command(): void
    {
        $app = new Application();
        $app->add(new TestCommand());
        $this->assertSame(['test:cmd'], $app->names());
    }

    public function test_application_get_command(): void
    {
        $app = new Application();
        $cmd = new TestCommand();
        $app->add($cmd);
        $this->assertSame($cmd, $app->get('test:cmd'));
        $this->assertNull($app->get('nonexistent'));
    }

    public function test_application_run_command(): void
    {
        $app = new Application();
        $app->add(new TestCommand());
        $exit = $app->run(['test:cmd', 'arg1', '--flag']);
        $this->assertSame(0, $exit);
    }

    public function test_application_run_unknown_returns_1(): void
    {
        $app = new Application();
        $exit = $app->run(['unknown:command']);
        $this->assertSame(1, $exit);
    }

    public function test_application_empty_args_prints_help(): void
    {
        $app = new Application();
        $exit = $app->run([]);
        $this->assertSame(0, $exit);
    }

    public function test_command_argument_and_option_methods(): void
    {
        $cmd = new TestCommand();
        $cmd->configure(['name' => 'ali'], ['verbose' => true, 'limit' => 10]);
        $this->assertSame('ali', $cmd->argument('name'));
        $this->assertSame('default', $cmd->argument('missing', 'default'));
        $this->assertTrue($cmd->option('verbose'));
        $this->assertSame(10, $cmd->option('limit'));
        $this->assertTrue($cmd->hasOption('verbose'));
        $this->assertFalse($cmd->hasOption('nonexistent'));
        $this->assertTrue($cmd->hasArgument('name'));
        $this->assertFalse($cmd->hasArgument('missing'));
    }

    public function test_command_arguments_options_arrays(): void
    {
        $cmd = new TestCommand();
        $cmd->configure(['a' => 1, 'b' => 2], ['x' => true]);
        $this->assertSame(['a' => 1, 'b' => 2], $cmd->arguments());
        $this->assertSame(['x' => true], $cmd->options());
    }

    public function test_command_run_calls_handle(): void
    {
        $cmd = new TestCommand();
        $exit = $cmd->run(['arg1'], ['opt1' => 'val1']);
        $this->assertSame(0, $exit);
        $this->assertSame('arg1', $cmd->argument('0'));
    }

    public function test_command_output_methods(): void
    {
        $cmd = new TestCommand();
        \ob_start();
        $cmd->info('info msg');
        $cmd->error('error msg');
        $cmd->warn('warn msg');
        $cmd->comment('comment msg');
        $cmd->line('plain');
        $cmd->newLine(2);
        $output = \ob_get_clean();
        $this->assertStringContainsString('info msg', $output);
        $this->assertStringContainsString('error msg', $output);
        $this->assertStringContainsString('warn msg', $output);
        $this->assertStringContainsString('comment msg', $output);
        $this->assertStringContainsString('plain', $output);
    }

    public function test_command_table_output(): void
    {
        $cmd = new TestCommand();
        \ob_start();
        $cmd->table(['Name', 'Age'], [['Ali', '30'], ['Sara', '25']]);
        $output = \ob_get_clean();
        $this->assertStringContainsString('Name', $output);
        $this->assertStringContainsString('Age', $output);
        $this->assertStringContainsString('Ali', $output);
        $this->assertStringContainsString('Sara', $output);
    }

    public function test_command_alert(): void
    {
        $cmd = new TestCommand();
        \ob_start();
        $cmd->alert('Warning!');
        $output = \ob_get_clean();
        $this->assertStringContainsString('Warning!', $output);
        $this->assertStringContainsString('!', $output);
    }

    public function test_list_command_lists_commands(): void
    {
        $app = new Application();
        $app->add(new TestCommand());
        $listCmd = new ListCommand($app);
        \ob_start();
        $listCmd->handle();
        $output = \ob_get_clean();
        $this->assertStringContainsString('test:cmd', $output);
    }
}

final class TestCommand extends Command
{
    protected const NAME = 'test:cmd';
    protected const DESCRIPTION = 'A test command';

    public function handle(): int
    {
        $this->line('test command executed');
        return self::SUCCESS;
    }
}
