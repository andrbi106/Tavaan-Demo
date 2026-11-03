<?php
declare(strict_types=1);
namespace Tavaan\Engine\Payment;

final class PaymentRequest
{
    public function __construct(
        public readonly string $id,
        public readonly int $amount,
        public readonly string $currency = 'IRR',
        public readonly ?string $description = null,
        public readonly ?string $callbackUrl = null,
        public ?string $gatewayUrl = null,
        public ?string $authority = null,
        public string $status = 'pending',
    ) {}
}

final class PaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $transactionId = null,
        public readonly ?string $message = null,
        public readonly ?string $gatewayUrl = null,
        public readonly ?PaymentRequest $request = null,
    ) {}
}

final class PaymentManager
{
    private array $gateways = [];
    private string $defaultGateway;

    public function __construct(private readonly array $config = [])
    {
        $this->defaultGateway = $config['default'] ?? 'mock';
        $this->gateways['mock'] = new MockGateway();
    }

    public function addGateway(string $name, object $gateway): self { $this->gateways[$name] = $gateway; return $this; }
    public function gateway(?string $name = null): object { return $this->gateways[$name ?? $this->defaultGateway] ?? $this->gateways['mock']; }

    public function request(int $amount, array $options = []): PaymentResult
    {
        $id = bin2hex(random_bytes(16));
        $request = new PaymentRequest($id, $amount, $options['currency'] ?? 'IRR', $options['description'] ?? null, $options['callback_url'] ?? null);
        return $this->gateway()->purchase($request);
    }

    public function verify(string $authority, int $amount): PaymentResult { return $this->gateway()->verify($authority, $amount); }
    public function refund(string $transactionId, int $amount): PaymentResult { return $this->gateway()->refund($transactionId, $amount); }
    public function getDefaultGateway(): string { return $this->defaultGateway; }
    public function getGateways(): array { return array_keys($this->gateways); }
}

final class MockGateway
{
    public function purchase(PaymentRequest $request): PaymentResult
    {
        $request->gatewayUrl = 'https://mock-gateway.example.com/pay/' . $request->id;
        $request->authority = 'mock_' . $request->id;
        return new PaymentResult(true, $request->id, 'Mock payment initiated', $request->gatewayUrl, $request);
    }

    public function verify(string $authority, int $amount): PaymentResult
    {
        if (str_starts_with($authority, 'mock_')) return new PaymentResult(true, $authority, 'Payment verified');
        return new PaymentResult(false, null, 'Invalid authority');
    }

    public function refund(string $transactionId, int $amount): PaymentResult { return new PaymentResult(true, $transactionId, 'Refund processed'); }
}

final class PaymentServiceProvider
{
    private ?PaymentManager $manager = null;
    public function __construct(private readonly array $config = []) {}
    public function register(): void { $this->manager = new PaymentManager($this->config); }
    public function boot(): void {}
    public function provides(): array { return [PaymentManager::class]; }
    public function getManager(): PaymentManager { if ($this->manager === null) $this->register(); return $this->manager; }

    public function inquire(string $authority, int $amount): PaymentResult
    {
        return $this->getManager()->verify($authority, $amount);
    }
}
