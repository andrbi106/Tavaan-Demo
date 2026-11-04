# Tavaan Engine — Payment

> **پرداخت** — موتور Payment در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.payment` |
| نام | `Payment` |
| عنوان فارسی | پرداخت |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Payment` |
| کلاس موتور | `Tavaan\Engine\Payment\PaymentEngine` |
| Facade | Payment (`payment`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Payment;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class PaymentEngine extends BaseEngine
{
    public function __construct(EngineManifestInterface $manifest)
    {
        parent::__construct($manifest);
    }

    public function register(ContainerInterface $container): void
    {
        parent::register($container);
        // bindingهای اختصاصی موتور
    }

    public function boot(): void
    {
        parent::boot();
    }
}
```

**فایل:** `src/PaymentEngine.php`

## توضیحات

M7 — انتزاع درگاه‌های پرداخت با GatewayInterface مشترک (core/Contracts/Payment): زرین‌پال/آیدی‌پی/پی‌پینگ/سامان/به‌پرداخت/پارسیان/سداد/ملی/Stripe (خودنوشته، بدون SDK) + کیف پول داخلی (WalletStoreInterface). 🔒 امنیت: verify فقط از API درگاه با مبلغ محلی Transaction (ضد amount tampering)، عدم اعتماد به Status بازگشتی، idempotency_key یکتا (ضد replay/double payment)، PAN mask، امضای HMAC برای IPN، SSRF guard موتور http-client. routeهای /payment/callback و /payment/notify/{gateway}. درگاه سفارشی فقط با extend() یا config class (AC-PAY-10).

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`

**موتورهای اختیاری:** `http-client`, `database`, `migration`, `orm`, `event`, `crypt`, `http`, `console`

## Service Providerها

- `Tavaan\Engine\Payment\PaymentManagerServiceProvider`

## جداول دیتابیس

- `tvn_payment_transactions`

## دستورات CLI

- `payment:list`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Payment.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
