# ساختار پوشهٔ مرجع تَوان

## ساختار کامل

```
tavaan/
├── bin/                        # Entry Points (tvn, tvn.php, tavaan alias, worker.php)
├── public/                     # تنها پوشهٔ وب‌قابل‌دسترس (index.php, .htaccess, assets/, uploads/, media/)
├── core/                       # Tavaan\Core\ — هستهٔ صفر‌وابستگی
│   ├── bootstrap.php           # تنها require دستی مجاز
│   ├── Application/            # چرخهٔ عمر (Application, ApplicationBuilder, RuntimeMode)
│   ├── Autoloading/            # Autoloader, AutoloaderRegistry, AutoloadException
│   ├── Bootstrap/              # ۷ بوت‌استرپر (env, config, facades, providers, exceptions)
│   ├── Container/              # Container, Binding, Lifetime
│   ├── Context/                # RequestStack, TraceContext, IdentityContext
│   ├── Contracts/              # ۲۳ زیرمجموعه (Container, Events, Support, ...)
│   ├── Error/                  # ErrorHandler, LogMasker
│   ├── Events/                 # Event, EventDispatcher
│   ├── Exceptions/             # TavaanException + Core exceptions
│   ├── Facade/                 # Facade + Facades/
│   ├── Provider/               # ServiceProvider پایه
│   └── Support/                # Arr, Str, helpers, ValueObjects, Time, Id, Enum
├── engine-core/                # Tavaan\EngineCore\ — والد موتورها (Engine, EngineBus, EngineRegistry)
├── engines/                    # ۵۳ موتور خودنوشته
│   └── <name>/                 # هر موتور:
│       ├── module.json         # manifest
│       ├── src/                # ServiceProvider, Engine, Manager, Contracts, Drivers
│       ├── config/<name>.php   # پیکربندی
│       ├── lang/{fa,en}/       # ترجمه
│       ├── tests/{Unit,Feature,Benchmark,Fixtures}/
│       ├── benchmarks/         # <name>.bench.php
│       ├── docs/README.md      # لینک به مستند
│       └── examples/basic.php  # نمونهٔ اجراشدنی
├── config/                     # فایل‌های پیکربندی + schema/ + architecture.php
├── app/                        # App\ (Http/, Console/, Domain/, Providers/, Policies/)
├── modules/                    # ماژول‌های HMVC
├── kits/                       # کیت‌های اپلیکیشن
├── plugins/                    # افزونه‌های ثالث + plugins.lock
├── database/                   # migrations/, seeders/, factories/, schema/
├── routes/                     # web.php, api.php, channels.php, console.php
├── resources/                  # views/, lang/, css/, js/, fonts/, images/, stubs/
├── storage/                    # app/, cache/, framework/, logs/, backups/, temp/
├── tests/                      # Unit/, Feature/, Integration/, Benchmark/, Architecture/, Security/
├── docs/                       # مستندات
├── tools/                      # phpstan/, cs-fixer/, scripts/
├── examples/                   # hello-world/, blog-minimal/, api-only/
├── .env.example  .gitignore  .editorconfig
├── tavaan.json                 # manifest پروژه (اعتبارسنجی با project.v1.json)
├── LICENSE  CHANGELOG.md  CONTRIBUTING.md  SECURITY.md  README.md
```

## مجوزهای storage

| مسیر | مجوز |
|---|---|
| `storage/` | 0775 (www-data owner) |
| `storage/private/` | 0750 |
| `storage/backups/` | 0750 |
| session files | 0600 |
| log files | 0640 |

## قواعد دسترسی وب

- 🔒 `storage/`, `config/`, `core/` هرگز وب‌قابل‌دسترس نباشند
- 🔒 تنها `public/` وب‌قابل‌دسترس است

## درخت تصمیم محل فایل

```
آیا کلاس هسته است؟ → core/
  آیا Contract است؟ → core/Contracts/<Area>/
  آیا Support/Helper است؟ → core/Support/
آیا والد موتورهاست؟ → engine-core/
آیا موتور خاص است؟ → engines/<name>/src/
آیا کد اپلیکیشن است؟ → app/src/
آیا ماژول HMVC است؟ → modules/<name>/src/
آیا کیت است؟ → kits/<name>/src/
آیا افزونه ثالث است؟ → plugins/<name>/src/
آیا migration است؟ → database/migrations/
آیا تست است؟ → tests/ یا engines/<name>/tests/
آیا مستند است؟ → docs/
آیا ابزار توسعه است؟ → tools/
آیا نمونه است؟ → examples/
```

## ۵۳ موتور در ۸ دسته

| دسته | موتورها | فاز |
|---|---|---|
| Kernel (8) | container, config, event, pipeline, error, log, optimizer, module | M1 |
| Web (8) | http, router, session, cookie, view, api, realtime, cors | M2 |
| Data (7) | database, schema, migration, orm, validation, search, pagination | M3 |
| Security (6) | crypt, hash, auth, rbac, audit, security | M4 |
| Infrastructure (9) | cache, queue, schedule, console, filesystem, http-client, mail, notification, observability, backup | M5 |
| Localization (2) | i18n, jalali | M2 |
| Domain (5) | workflow, report, payment, media, feature-flag | M7 |
| Tooling (8) | scaffold, admin, plugin, test, upgrade, tenancy, compat | M6/M8 |

## ترتیب ساخت (۸ فاز)

```
M1: container → config → event → pipeline → error → log → compat → optimizer → module
M2: http → router → session → cookie → view → i18n → jalali → cors
M3: database → schema → migration → orm → pagination → validation
M4: crypt → hash → auth → rbac → security → cache → audit
M5: queue → schedule → console → filesystem → media → mail → notification → http-client → backup
M6: api → observability → search → module → tenancy → feature-flag → realtime
M7: workflow → report → payment
M8: scaffold → admin → plugin → test → upgrade
```
