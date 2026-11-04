# Tavaan Engine — Realtime

> **بلادرنگ** — موتور Realtime در فریمورک تَوان

این موتور از کلاس پایهٔ `Engine` (در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core)) ارث می‌برد.

## مشخصات

| فیلد | مقدار |
|---|---|
| شناسه | `tavaan.engine.realtime` |
| نام | `Realtime` |
| عنوان فارسی | بلادرنگ |
| نسخه | `0.1.0` |
| namespace | `Tavaan\Engine\Realtime` |
| کلاس موتور | `Tavaan\Engine\Realtime\RealtimeEngine` |
| Facade | Broadcast (`broadcast`), Realtime (`realtime.facade`) |

## کلاس موتور

```php
namespace Tavaan\Engine\Realtime;

use Tavaan\EngineCore\Engine as BaseEngine;
use Tavaan\EngineCore\Contracts\EngineManifestInterface;

final class RealtimeEngine extends BaseEngine
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

**فایل:** `src/RealtimeEngine.php`

## توضیحات

M6 — SSE بدون هیچ ext (StreamedResponse + heartbeat + retry)، WebSocket خودنوشته RFC 6455 (handshake/accept-key، frame codec با mask اجباری client، fragmentation، ping/pong، Origin check)، brokerها: Array/Database(polling با TTL prune)/Redis(bازاستفاده از RedisClient صف با seq سراسری و fallback خودکار)، ChannelRegistry با {param}/wildcard، احراز کانال خصوصی (POST /broadcasting/auth + توکن HMAC)، Presence با cache TTL، قلاب ShouldBroadcast در EventDispatcher، routes/channels.php.

## وابستگی‌ها

**موتورهای مورد‌نیاز:** `config`, `event`, `http`

**موتورهای اختیاری:** `database`, `cache`, `queue`, `auth`

## Service Providerها

- `Tavaan\Engine\Realtime\RealtimeManagerServiceProvider`

## دستورات CLI

- `realtime:websocket`

## سه درگاه ارتباط با سایر موتورها

| درگاه | الگو | متدهای convenience |
|---|---|---|
| Container | pull-based DI | `make()`, `bound()` |
| Events | pub/sub | `dispatch()`, `listen()` |
| EngineBus | point-to-point RPC | `call()`, `offer()` |

## نصب

```bash
git clone https://github.com/andrbi106/Tavaan-Engine-Realtime.git
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
