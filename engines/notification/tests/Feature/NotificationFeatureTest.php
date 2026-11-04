<?php

declare(strict_types=1);

namespace Tavaan\Engine\Notification\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Notification\NotificationManager;
use Tavaan\Engine\Notification\NotificationServiceProvider;

final class NotificationFeatureTest extends TestCase
{
    public function test_service_provider_creates_manager(): void
    {
        $provider = new NotificationServiceProvider();
        $provider->register();
        $this->assertInstanceOf(NotificationManager::class, $provider->getManager());
    }

    public function test_service_provider_provides(): void
    {
        $provider = new NotificationServiceProvider();
        $this->assertContains(NotificationManager::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/notification.php';
        $this->assertArrayHasKey('channels', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $m = require __DIR__ . '/../../lang/fa/notification.php';
        $this->assertArrayHasKey('error_send', $m);
    }
}
