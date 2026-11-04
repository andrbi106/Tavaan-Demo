# Tavaan-Demo

> مخزن نمایشی تَوان — شامل هستهٔ فریمورک، موتور والد، و ۵۲ موتور فرزند.

## ساختار

```
Tavaan-Demo/
├── core/                  ← Tavaan-Core (هستهٔ فریمورک)
│   ├── Application/       چرخهٔ عمر اپلیکیشن
│   ├── Autoloading/       Autoloader صفر‌وابستگی
│   ├── Bootstrap/         ۷ بوت‌استرپر
│   ├── Container/         DI Container (درگاه ۱)
│   ├── Context/           Trace/Identity/RequestStack
│   ├── Contracts/         قراردادهای Container/Events/Support
│   ├── Error/             ErrorHandler + LogMasker
│   ├── Events/            EventDispatcher (درگاه ۲)
│   ├── Facade/            Facade پایه + App/Event
│   ├── Provider/          ServiceProvider پایه
│   ├── Support/           Arr, Str, Clock, Ulid, PathGuard, helpers
│   ├── bootstrap.php      نقطهٔ ورود
│   └── composer.json
│
├── engine-core/           ← Tavaan-Engine-Core (والد موتورها)
│   ├── Contracts/         EngineInterface + ۳ قرارداد دیگر
│   ├── Exceptions/        EngineBoot/Capability/NotInstalled
│   ├── Engine.php         کلاس پایه (همه ارث می‌برند)
│   ├── EngineBus.php      درگاه ۳: point-to-point RPC
│   ├── EngineContext.php  data اشتراکی
│   ├── EngineLoader.php   کشف از filesystem
│   ├── EngineManifest.php value object
│   ├── EngineRegistry.php topological boot
│   └── EngineServiceProvider.php
│
├── engines/               ← ۵۲ موتور فرزند
│   ├── auth/              احراز هویت
│   ├── cache/             کش
│   ├── config/            پیکربندی
│   ├── console/           CLI
│   ├── database/          دیتابیس + ORM + Migration
│   ├── http/              HTTP + Routing + Cookie + Session
│   ├── log/               لاگ
│   ├── validation/        اعتبارسنجی
│   └── ...                ۴۴ موتور دیگر
│
└── README.md              این فایل
```

## معماری

```
Tavaan-Core (هستهٔ فریمورک)
   Container.php, EventDispatcher.php, ServiceProvider, Facade, ...
       ↑
       |  Engine-Core از Container, Events, ServiceProvider, TavaanException استفاده می‌کند
       |
Tavaan-Engine-Core (والد موتورها)
   Engine base class + EngineBus + EngineRegistry/Loader/Manifest/Context
       ↑
       |  هر موتور از Engine ارث می‌برد (extends BaseEngine)
       |
۵۲ موتور فرزند (Auth, Cache, Http, Database, ...)
```

## سه درگاه ارتباط بین موتورها

| درگاه | پیاده‌سازی | الگو | کاربرد |
|---|---|---|---|
| **Container** | Tavaan-Core | pull-based DI | `$this->make(CacheInterface::class)` |
| **Events** | Tavaan-Core | pub/sub | `$this->dispatch('user.registered', $data)` |
| **EngineBus** | Tavaan-Engine-Core | point-to-point RPC | `$this->call('auth', 'issue-token', $input)` |

## آمار

- **هستهٔ فریمورک:** Tavaan-Core (۴۵ فایل)
- **موتور والد:** Tavaan-Engine-Core (۱۴ فایل)
- **موتورهای فرزند:** ۵۲ موتور
- **کل فایل‌ها:** 1452 فایل PHP

## فهرست موتورها

| نام | مخزن | مسیر |
|---|---|---|
| API | [`Tavaan-Engine-API`](https://github.com/andrbi106/Tavaan-Engine-API) | `engines/api/` |
| Admin | [`Tavaan-Engine-Admin`](https://github.com/andrbi106/Tavaan-Engine-Admin) | `engines/admin/` |
| Audit | [`Tavaan-Engine-Audit`](https://github.com/andrbi106/Tavaan-Engine-Audit) | `engines/audit/` |
| Auth | [`Tavaan-Engine-Auth`](https://github.com/andrbi106/Tavaan-Engine-Auth) | `engines/auth/` |
| Backup | [`Tavaan-Engine-Backup`](https://github.com/andrbi106/Tavaan-Engine-Backup) | `engines/backup/` |
| CORS | [`Tavaan-Engine-CORS`](https://github.com/andrbi106/Tavaan-Engine-CORS) | `engines/cors/` |
| Cache | [`Tavaan-Engine-Cache`](https://github.com/andrbi106/Tavaan-Engine-Cache) | `engines/cache/` |
| Compat | [`Tavaan-Engine-Compat`](https://github.com/andrbi106/Tavaan-Engine-Compat) | `engines/compat/` |
| Config | [`Tavaan-Engine-Config`](https://github.com/andrbi106/Tavaan-Engine-Config) | `engines/config/` |
| Console | [`Tavaan-Engine-Console`](https://github.com/andrbi106/Tavaan-Engine-Console) | `engines/console/` |
| Container | [`Tavaan-Engine-Container`](https://github.com/andrbi106/Tavaan-Engine-Container) | `engines/container/` |
| Cookie | [`Tavaan-Engine-Cookie`](https://github.com/andrbi106/Tavaan-Engine-Cookie) | `engines/cookie/` |
| Crypt | [`Tavaan-Engine-Crypt`](https://github.com/andrbi106/Tavaan-Engine-Crypt) | `engines/crypt/` |
| Database | [`Tavaan-Engine-Database`](https://github.com/andrbi106/Tavaan-Engine-Database) | `engines/database/` |
| Error | [`Tavaan-Engine-Error`](https://github.com/andrbi106/Tavaan-Engine-Error) | `engines/error/` |
| Event | [`Tavaan-Engine-Event`](https://github.com/andrbi106/Tavaan-Engine-Event) | `engines/event/` |
| Feature-Flag | [`Tavaan-Engine-Feature-Flag`](https://github.com/andrbi106/Tavaan-Engine-Feature-Flag) | `engines/feature-flag/` |
| Filesystem | [`Tavaan-Engine-Filesystem`](https://github.com/andrbi106/Tavaan-Engine-Filesystem) | `engines/filesystem/` |
| Hash | [`Tavaan-Engine-Hash`](https://github.com/andrbi106/Tavaan-Engine-Hash) | `engines/hash/` |
| Http | [`Tavaan-Engine-Http`](https://github.com/andrbi106/Tavaan-Engine-Http) | `engines/http/` |
| Http-Client | [`Tavaan-Engine-Http-Client`](https://github.com/andrbi106/Tavaan-Engine-Http-Client) | `engines/http-client/` |
| I18n | [`Tavaan-Engine-I18n`](https://github.com/andrbi106/Tavaan-Engine-I18n) | `engines/i18n/` |
| Jalali | [`Tavaan-Engine-Jalali`](https://github.com/andrbi106/Tavaan-Engine-Jalali) | `engines/jalali/` |
| Log | [`Tavaan-Engine-Log`](https://github.com/andrbi106/Tavaan-Engine-Log) | `engines/log/` |
| Mail | [`Tavaan-Engine-Mail`](https://github.com/andrbi106/Tavaan-Engine-Mail) | `engines/mail/` |
| Media | [`Tavaan-Engine-Media`](https://github.com/andrbi106/Tavaan-Engine-Media) | `engines/media/` |
| Migration | [`Tavaan-Engine-Migration`](https://github.com/andrbi106/Tavaan-Engine-Migration) | `engines/migration/` |
| Module | [`Tavaan-Engine-Module`](https://github.com/andrbi106/Tavaan-Engine-Module) | `engines/module/` |
| Notification | [`Tavaan-Engine-Notification`](https://github.com/andrbi106/Tavaan-Engine-Notification) | `engines/notification/` |
| ORM | [`Tavaan-Engine-ORM`](https://github.com/andrbi106/Tavaan-Engine-ORM) | `engines/orm/` |
| Observability | [`Tavaan-Engine-Observability`](https://github.com/andrbi106/Tavaan-Engine-Observability) | `engines/observability/` |
| Optimizer | [`Tavaan-Engine-Optimizer`](https://github.com/andrbi106/Tavaan-Engine-Optimizer) | `engines/optimizer/` |
| Payment | [`Tavaan-Engine-Payment`](https://github.com/andrbi106/Tavaan-Engine-Payment) | `engines/payment/` |
| Pipeline | [`Tavaan-Engine-Pipeline`](https://github.com/andrbi106/Tavaan-Engine-Pipeline) | `engines/pipeline/` |
| Plugin | [`Tavaan-Engine-Plugin`](https://github.com/andrbi106/Tavaan-Engine-Plugin) | `engines/plugin/` |
| Queue | [`Tavaan-Engine-Queue`](https://github.com/andrbi106/Tavaan-Engine-Queue) | `engines/queue/` |
| RBAC | [`Tavaan-Engine-RBAC`](https://github.com/andrbi106/Tavaan-Engine-RBAC) | `engines/rbac/` |
| Realtime | [`Tavaan-Engine-Realtime`](https://github.com/andrbi106/Tavaan-Engine-Realtime) | `engines/realtime/` |
| Report | [`Tavaan-Engine-Report`](https://github.com/andrbi106/Tavaan-Engine-Report) | `engines/report/` |
| Router | [`Tavaan-Engine-Router`](https://github.com/andrbi106/Tavaan-Engine-Router) | `engines/router/` |
| Scaffold | [`Tavaan-Engine-Scaffold`](https://github.com/andrbi106/Tavaan-Engine-Scaffold) | `engines/scaffold/` |
| Schedule | [`Tavaan-Engine-Schedule`](https://github.com/andrbi106/Tavaan-Engine-Schedule) | `engines/schedule/` |
| Schema | [`Tavaan-Engine-Schema`](https://github.com/andrbi106/Tavaan-Engine-Schema) | `engines/schema/` |
| Search | [`Tavaan-Engine-Search`](https://github.com/andrbi106/Tavaan-Engine-Search) | `engines/search/` |
| Security | [`Tavaan-Engine-Security`](https://github.com/andrbi106/Tavaan-Engine-Security) | `engines/security/` |
| Session | [`Tavaan-Engine-Session`](https://github.com/andrbi106/Tavaan-Engine-Session) | `engines/session/` |
| Tenancy | [`Tavaan-Engine-Tenancy`](https://github.com/andrbi106/Tavaan-Engine-Tenancy) | `engines/tenancy/` |
| Test | [`Tavaan-Engine-Test`](https://github.com/andrbi106/Tavaan-Engine-Test) | `engines/test/` |
| Upgrade | [`Tavaan-Engine-Upgrade`](https://github.com/andrbi106/Tavaan-Engine-Upgrade) | `engines/upgrade/` |
| Validation | [`Tavaan-Engine-Validation`](https://github.com/andrbi106/Tavaan-Engine-Validation) | `engines/validation/` |
| View | [`Tavaan-Engine-View`](https://github.com/andrbi106/Tavaan-Engine-View) | `engines/view/` |
| Workflow | [`Tavaan-Engine-Workflow`](https://github.com/andrbi106/Tavaan-Engine-Workflow) | `engines/workflow/` |

## نحوه استفاده

این مخزن **نمایشی** است — برای توسعه به مخازن اصلی مراجعه کنید.

```bash
# کلون همهٔ مخازن
git clone https://github.com/andrbi106/Tavaan-Core.git
git clone https://github.com/andrbi106/Tavaan-Engine-Core.git
git clone https://github.com/andrbi106/Tavaan-Engine-Auth.git
# ...

# یا از این مخزن نمایشی برای مرور استفاده کنید
```

## مجوز

MIT — هم‌راستا با فریمورک مادر تَوان.
