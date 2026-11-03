<?php

declare(strict_types=1);

namespace Tavaan\Engine\Mail;

use Tavaan\Engine\Mail\Contracts\TransportInterface;

final class MailServiceProvider
{
    private ?MailManager $manager = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->manager = new MailManager($this->config);
    }

    public function boot(): void {}

    public function provides(): array
    {
        return [MailManager::class, TransportInterface::class];
    }

    public function getManager(): MailManager
    {
        if ($this->manager === null) $this->register();
        return $this->manager;
    }
}
