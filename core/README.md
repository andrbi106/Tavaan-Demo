# Tavaan-Core

> **هستهٔ فریمورک تَوان** — کانتینر DI، Event Dispatcher، Autoloader، قراردادها و زیرساخت بنیادی.

## نقش

این مخزن **هستهٔ فریمورک** تَوان است — کتابخانهٔ زیرساختی که همهٔ چیزهای دیگر روی آن ساخته می‌شوند.

این مخزن **اسکلت اپلیکیشن نیست** (app/، routes/، config/، public/). اسکلت در صورت نیاز در مخزن جداگانه‌ای قرار خواهد گرفت.

## وابستگی‌ها

```
Tavaan-Core (این مخزن — هسته فریمورک)
    ↑
    |  Tavaan-Engine-Core از Container, Events, ServiceProvider, TavaanException استفاده می‌کند
    |
Tavaan-Engine-Core (والد موتورها)
    ↑
    |  هر موتور از Engine ارث می‌برد + از Facade/Container/Events استفاده می‌کند
    |
52 موتور فرزند
```

## چه چیزی ارائه می‌دهد

### درگاه‌های ارتباط (۲ از ۳ درگاه)

| درگاه | فایل | الگو |
|---|---|---|
| **Container** | `Container/Container.php` | pull-based DI |
| **Events** | `Events/EventDispatcher.php` | pub/sub |

درگاه سوم (EngineBus) در [Tavaan-Engine-Core](https://github.com/andrbi106/Tavaan-Engine-Core) پیاده‌سازی شده است.

### قراردادها (Contracts)

| قرارداد | فایل |
|---|---|
| `ContainerInterface` | `Contracts/Container/ContainerInterface.php` |
| `PsrContainerInterface` | `Contracts/Container/PsrContainerInterface.php` |
| `DispatcherInterface` | `Contracts/Events/DispatcherInterface.php` |
| `EventSubscriber` | `Contracts/Events/EventSubscriber.php` |
| `ShouldBroadcast` | `Contracts/Events/ShouldBroadcast.php` |
| `ShouldQueue` | `Contracts/Events/ShouldQueue.php` |
| `StoppableEventInterface` | `Contracts/Events/StoppableEventInterface.php` |
| `Arrayable`, `Jsonable`, `Renderable`, `DeferrableProvider` | `Contracts/Support/` |

### زیرساخت بنیادی

| بخش | فایل‌ها | نقش |
|---|---|---|
| **Container** | `Container/Container.php` | DI کانتینر با reflection-based auto-resolution |
| **Events** | `Events/EventDispatcher.php` | pub/sub event system |
| **Autoloading** | `Autoloading/Autoloader.php` | PSR-4 + wildcard + path traversal prevention |
| **Application** | `Application/Application.php` | چرخهٔ عمر اپلیکیشن |
| **Bootstrap** | `Bootstrap/*.php` | ۷ بوت‌استرپر (env, config, facades, providers, exceptions) |
| **Provider** | `Provider/ServiceProvider.php` | کلاس پایه برای همهٔ Service Providerها |
| **Facade** | `Facade/Facade.php` | کلاس پایه برای همهٔ Facadeها |
| **Exceptions** | `Exceptions/*.php` | TavaanException (پایه) + Core exceptions |
| **Context** | `Context/*.php` | TraceContext, IdentityContext, RequestStack |
| **Error** | `Error/*.php` | ErrorHandler, LogMasker |
| **Support** | `Support/*.php` | Arr, Str, helpers |
| **bootstrap.php** | `bootstrap.php` | نقطهٔ ورود |

## ساختار src/

```
src/
├── Application/
│   ├── Application.php              چرخهٔ عمر (boot/shutdown)
│   ├── ApplicationBuilder.php       fluent builder
│   ├── ApplicationInterface.php
│   └── RuntimeMode.php              production/development/testing/console
├── Autoloading/
│   ├── Autoloader.php               PSR-4 + wildcard + security
│   ├── AutoloaderRegistry.php       global access
│   └── AutoloadException.php
├── Bootstrap/
│   ├── BootstrapperInterface.php
│   ├── LoadEnvironmentVariables.php
│   ├── LoadConfiguration.php
│   ├── RegisterFacades.php
│   ├── RegisterProviders.php
│   ├── BootProviders.php
│   └── HandleExceptions.php
├── Container/
│   ├── Container.php                DI container (درگاه ۱)
│   ├── Binding.php                  value object
│   └── Lifetime.php                 enum: transient/singleton
├── Contracts/
│   ├── Container/                   ContainerInterface, PsrContainerInterface
│   ├── Events/                      DispatcherInterface + 4 more
│   └── Support/                     Arrayable, Jsonable, Renderable, DeferrableProvider
├── Context/
│   ├── IdentityContext.php          user_id, tenant_id, roles
│   ├── TraceContext.php             trace_id, span_id
│   └── RequestStack.php             nested requests
├── Error/
│   ├── ErrorHandler.php             error→exception conversion
│   └── LogMasker.php                ماسک دادهٔ حساس در لاگ
├── Events/
│   ├── Event.php                    base event class
│   └── EventDispatcher.php          pub/sub (درگاه ۲)
├── Exceptions/
│   ├── TavaanException.php          base (messageId + context + hint)
│   ├── ContextualException.php
│   ├── RenderableException.php
│   ├── ReportableException.php
│   ├── RetryableException.php
│   └── Core/
│       ├── ConfigException.php
│       ├── ContainerException.php
│       └── KernelException.php
├── Facade/
│   ├── Facade.php                   base (کلاس پایه برای همه facades)
│   └── Facades/
│       ├── App.php
│       └── Event.php
├── Provider/
│   └── ServiceProvider.php          base (کلاس پایه برای همه providers)
├── Support/
│   ├── Arr.php                      dot notation, flatten, only, except
│   ├── Str.php                      random, slug, camel, snake, studly
│   └── helpers.php                  global helpers
└── bootstrap.php                    نقطهٔ ورود
```

## نصب

```bash
composer require andrbi106/tavaan-core
```

## استفاده

```php
require dirname(__DIR__) . '/vendor/andrbi106/tavaan-core/src/bootstrap.php';

use Tavaan\Core\Application\ApplicationBuilder;

$app = ApplicationBuilder::create(__DIR__)->boot();

// درگاه ۱: Container (pull-based DI)
$cache = $app->make(CacheInterface::class);

// درگاه ۲: Events (pub/sub)
$app->make('events')->dispatch('user.registered', ['user_id' => 42]);
```

## این مخزن چیست و چیست نیست

| ✅ هست | ❌ نیست |
|---|---|
| کانتینر DI | app/ (controllers, models) |
| Event Dispatcher | routes/ |
| قراردادها (interfaces) | config/ (application config) |
| ServiceProvider پایه | public/ (index.php) |
| Facade پایه | resources/ (views, lang) |
| Autoloader | database/ (migrations) |
| Application lifecycle | storage/ (cache, logs) |
| Bootstrap | tests/ |
| Support utilities | tools/ |
| Error handling | examples/ |

## روابط با سایر مخازن

| مخزن | نقش |
|---|---|
| **`andrbi106/Tavaan-Core`** (این مخزن) | هستهٔ فریمورک — زیرساخت بنیادی |
| `andrbi106/Tavaan-Engine-Core` | والد موتورها — کلاس پایه + درگاه EngineBus + مدیریت |
| `andrbi106/Tavaan-Engine-*` (۵۲ مخزن) | موتورهای فرزند |
| `Derogaramin/Tavaan-Framework` | فریمورک مادر (monorepo) |

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
