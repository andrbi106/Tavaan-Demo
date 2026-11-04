<?php

declare(strict_types=1);

namespace Tavaan\Engine\View\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\View\View;
use Tavaan\Engine\View\ViewFactory;
use Tavaan\Engine\View\Escaper;
use Tavaan\Engine\View\TemplateCompiler;

final class ViewTest extends TestCase
{
    public function test_escaper_html(): void
    {
        $this->assertSame('&lt;script&gt;', Escaper::html('<script>'));
        $this->assertSame('&amp;', Escaper::html('&'));
        $this->assertSame('&quot;hi&quot;', Escaper::html('"hi"'));
    }

    public function test_escaper_url(): void
    {
        $this->assertSame('hello%20world', Escaper::url('hello world'));
    }

    public function test_view_inline_render(): void
    {
        $view = new View('Hello, {{ $name }}!', ['name' => 'Ali']);
        $this->assertSame('Hello, Ali!', $view->render());
    }

    public function test_view_escapes_html(): void
    {
        $view = new View('{{ $x }}', ['x' => '<script>']);
        $this->assertSame('&lt;script&gt;', $view->render());
    }

    public function test_view_raw_output(): void
    {
        $view = new View('{!! $x !!}', ['x' => '<b>bold</b>']);
        $this->assertSame('<b>bold</b>', $view->render());
    }

    public function test_view_with_method(): void
    {
        $view = (new View('{{ $name }}'))->with('name', 'Ali');
        $this->assertSame('Ali', $view->render());
    }

    public function test_view_with_data_method(): void
    {
        $view = (new View('{{ $a }} {{ $b }}'))->withData(['a' => '1', 'b' => '2']);
        $this->assertSame('1 2', $view->render());
    }

    public function test_view_if_directive(): void
    {
        $view = new View('@if($show)visible@endif', ['show' => true]);
        $this->assertSame('visible', $view->render());
        $view2 = new View('@if($show)visible@endif', ['show' => false]);
        $this->assertSame('', $view2->render());
    }

    public function test_view_if_else(): void
    {
        $view = new View('@if($x)yes@else no@endif', ['x' => true]);
        $this->assertSame('yes', $view->render());
    }

    public function test_view_foreach_directive(): void
    {
        $view = new View('@foreach($items as $item)[{{ $item }}]@endforeach', ['items' => [1, 2, 3]]);
        $this->assertSame('[1][2][3]', $view->render());
    }

    public function test_view_for_directive(): void
    {
        $view = new View('@for($i = 0; $i < 3; $i++){{ $i }}@endfor', []);
        $this->assertSame('012', $view->render());
    }

    public function test_view_while_directive(): void
    {
        $view = new View('<?php $i = 0; ?>@while($i < 3){{ $i }}<?php $i++; ?>@endwhile', []);
        $this->assertSame('012', $view->render());
    }

    public function test_view_php_directive(): void
    {
        $view = new View('@php $x = "hello"; @endphp{{ $x }}', []);
        $this->assertSame('hello', $view->render());
    }

    public function test_view_isset_directive(): void
    {
        $view = new View('@isset($x)yes@endisset', ['x' => 1]);
        $this->assertSame('yes', $view->render());
        $view2 = new View('@isset($x)yes@endisset', []);
        $this->assertSame('', $view2->render());
    }

    public function test_view_empty_directive(): void
    {
        $view = new View('@empty($x)empty@endempty', []);
        $this->assertSame('empty', $view->render());
    }

    public function test_view_to_string(): void
    {
        $view = new View('Hi {{ $name }}', ['name' => 'Ali']);
        $this->assertSame('Hi Ali', (string) $view);
    }

    public function test_view_get_name(): void
    {
        $view = new View('my-template', []);
        $this->assertSame('my-template', $view->getName());
    }

    public function test_view_get_data(): void
    {
        $view = new View('t', ['a' => 1]);
        $this->assertSame(['a' => 1], $view->getData());
    }

    public function test_view_factory_make_inline(): void
    {
        $factory = new ViewFactory();
        $view = $factory->make('Hello {{ $name }}', ['name' => 'Ali']);
        $this->assertSame('Hello Ali', $view->render());
    }

    public function test_view_factory_share(): void
    {
        $factory = new ViewFactory();
        $factory->share('app_name', 'Tavaan');
        $view = $factory->make('Welcome to {{ $app_name }}');
        $this->assertSame('Welcome to Tavaan', $view->render());
    }

    public function test_view_factory_exists_for_file(): void
    {
        $tmpDir = \sys_get_temp_dir() . '/tvn_views_' . \uniqid();
        \mkdir($tmpDir);
        \file_put_contents($tmpDir . '/test.tv.php', 'Hello');
        $factory = new ViewFactory(viewPath: $tmpDir);
        $this->assertTrue($factory->exists('test'));
        \unlink($tmpDir . '/test.tv.php');
        \rmdir($tmpDir);
    }

    public function test_view_factory_render_file(): void
    {
        $tmpDir = \sys_get_temp_dir() . '/tvn_views_' . \uniqid();
        \mkdir($tmpDir);
        \file_put_contents($tmpDir . '/greeting.tv.php', 'Hello, {{ $name }}!');
        $factory = new ViewFactory(viewPath: $tmpDir);
        $view = $factory->make('greeting', ['name' => 'Ali']);
        $this->assertSame('Hello, Ali!', $view->render());
        \unlink($tmpDir . '/greeting.tv.php');
        \rmdir($tmpDir);
    }

    public function test_compiler_compiles_echo(): void
    {
        $c = new TemplateCompiler();
        $result = $c->compile('{{ $x }}');
        $this->assertStringContainsString('Escaper::html', $result);
        $this->assertStringContainsString('<?php echo', $result);
    }

    public function test_compiler_compiles_raw(): void
    {
        $c = new TemplateCompiler();
        $result = $c->compile('{!! $x !!}');
        $this->assertStringContainsString('<?php echo $x; ?>', $result);
    }

    public function test_compiler_compiles_directives(): void
    {
        $c = new TemplateCompiler();
        $result = $c->compile('@if($x)hi@endif');
        $this->assertStringContainsString('<?php if', $result);
        $this->assertStringContainsString('endif', $result);
    }

    public function test_compiler_verbatim_protection(): void
    {
        $c = new TemplateCompiler();
        $result = $c->compile('@verbatim{{ $x }}@endverbatim');
        $this->assertSame('{{ $x }}', $result);
    }
}
