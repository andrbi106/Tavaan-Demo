<?php

declare(strict_types=1);

namespace Tavaan\Engine\View\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\View\View;
use Tavaan\Engine\View\ViewFactory;
use Tavaan\Engine\View\ViewServiceProvider;

final class ViewFeatureTest extends TestCase
{
    public function test_service_provider_creates_factory(): void
    {
        $provider = new ViewServiceProvider([]);
        $provider->register();
        $this->assertInstanceOf(ViewFactory::class, $provider->getFactory());
    }

    public function test_service_provider_provides(): void
    {
        $provider = new ViewServiceProvider([]);
        $this->assertContains(ViewFactory::class, $provider->provides());
        $this->assertContains(View::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/view.php';
        $this->assertArrayHasKey('path', $config);
        $this->assertArrayHasKey('compiled', $config);
        $this->assertArrayHasKey('extension', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/view.php';
        $this->assertArrayHasKey('error_template', $messages);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/view.php';
        $this->assertArrayHasKey('error_template', $messages);
    }

    public function test_end_to_end_view_rendering(): void
    {
        $provider = new ViewServiceProvider([]);
        $provider->register();
        $factory = $provider->getFactory();
        $view = $factory->make('Hello, {{ $name }}! You have {{ $count }} messages.', [
            'name' => 'Ali',
            'count' => 5,
        ]);
        $this->assertSame('Hello, Ali! You have 5 messages.', $view->render());
    }
}
