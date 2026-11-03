<?php

declare(strict_types=1);

namespace Tavaan\Engine\Notifications;

final class NotificationServiceProvider
{
    private ?NotificationManager $manager = null;

    public function __construct() {}

    public function register(): void
    {
        $this->manager = new NotificationManager();
    }

    public function boot(): void {}

    public function provides(): array
    {
        return [NotificationManager::class];
    }

    public function getManager(): NotificationManager
    {
        if ($this->manager === null) $this->register();
        return $this->manager;
    }
}
