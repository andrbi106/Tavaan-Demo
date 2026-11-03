<?php
declare(strict_types=1);
namespace Tavaan\Engine\Realtime;

final class Channel
{
    private array $subscribers = [];

    public function __construct(public readonly string $name) {}

    public function subscribe(string $connectionId, callable $callback): void
    {
        $this->subscribers[$connectionId] = $callback;
    }

    public function unsubscribe(string $connectionId): void
    {
        unset($this->subscribers[$connectionId]);
    }

    public function broadcast(string $event, mixed $data): void
    {
        foreach ($this->subscribers as $callback) { $callback($event, $data); }
    }

    public function count(): int { return count($this->subscribers); }
}

final class RealtimeManager
{
    private array $channels = [];

    public function channel(string $name): Channel
    {
        if (!isset($this->channels[$name])) $this->channels[$name] = new Channel($name);
        return $this->channels[$name];
    }

    public function broadcast(string $channel, string $event, mixed $data): void
    {
        $this->channel($channel)->broadcast($event, $data);
    }

    public function subscribe(string $channel, string $connectionId, callable $callback): void
    {
        $this->channel($channel)->subscribe($connectionId, $callback);
    }

    public function unsubscribe(string $channel, string $connectionId): void
    {
        $this->channel($channel)->unsubscribe($connectionId);
    }

    public function getChannels(): array { return array_keys($this->channels); }
    public function channelCount(): int { return count($this->channels); }
}

final class RealtimeServiceProvider
{
    private ?RealtimeManager $manager = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->manager = new RealtimeManager(); }
    public function boot(): void {}
    public function provides(): array { return [RealtimeManager::class]; }
    public function getManager(): RealtimeManager { if ($this->manager === null) $this->register(); return $this->manager; }

    public function presence(string $channel): array
    {
        $mgr = $this->getManager();
        if (!in_array($channel, $mgr->getChannels(), true)) {
            return [];
        }
        $ch = $mgr->channel($channel);
        return [
            'channel' => $channel,
            'subscribers' => $ch->count(),
        ];
    }

    public function publish(string $channel, string $event, mixed $data): int
    {
        $mgr = $this->getManager();
        if (!in_array($channel, $mgr->getChannels(), true)) {
            return 0;
        }
        $ch = $mgr->channel($channel);
        $before = $ch->count();
        $ch->broadcast($event, $data);
        return $before; // # subscribers that received the event
    }
}
