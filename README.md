# تَوان | Tavaan — Demo v0.0.1

> فریمورک PHP 8.2+ فارسی‌اول — نسخهٔ نمایشی آمادهٔ راه‌اندازی.

## شروع سریع

```bash
# کلون
git clone https://github.com/andrbi106/Tavaan-Demo.git
cd Tavaan-Demo

# پیکربندی
cp .env.example .env

# اجرای سرور توسعه
php bin/tvn serve
```

سپس مرورگر را باز کنید: http://127.0.0.1:8000

## ساختار

```
Tavaan-Demo/
├── core/                 ← Tavaan-Core (هستهٔ فریمورک)
│   ├── Container/        DI Container (درگاه ۱)
│   ├── Events/           EventDispatcher (درگاه ۲)
│   ├── Autoloading/      Autoloader
│   ├── Application/      چرخهٔ عمر
│   ├── Bootstrap/        بوت‌استرپرها
│   ├── Provider/         ServiceProvider پایه
│   ├── Facade/           Facade پایه
│   ├── Support/          Arr, Str, Clock, Ulid, PathGuard
│   ├── Exceptions/       TavaanException + Core exceptions
│   └── bootstrap.php     نقطهٔ ورود هسته
│
├── engine-core/          ← Tavaan-Engine-Core (والد موتورها)
│   ├── Engine.php        کلاس پایه (درگاه ۳: EngineBus)
│   ├── EngineRegistry.php  topological boot
│   ├── EngineLoader.php  کشف از filesystem
│   └── ...
│
├── engines/              ← ۵۲ موتور فرزند
│   ├── auth/             احراز هویت
│   ├── cache/            کش
│   ├── database/         دیتابیس + ORM
│   ├── http/             HTTP + Routing
│   └── ...
│
├── app/
│   └── src/Controllers/  کنترلرهای اپلیکیشن
├── bin/
│   └── tvn               CLI entry point
├── config/
│   ├── app.php           پیکربندی اپلیکیشن
│   ├── autoload.php      نگاشت namespaceها
│   └── providers.php     Service Providerها
├── public/
│   └── index.php         HTTP entry point
├── routes/
│   ├── web.php           مسیرهای وب
│   ├── api.php           مسیرهای API
│   └── console.php       دستورات CLI
├── resources/
│   ├── lang/fa/          ترجمهٔ فارسی
│   └── lang/en/          ترجمهٔ انگلیسی
├── storage/
│   ├── logs/             لاگ‌ها
│   ├── cache/            کش
│   └── app/              دادهٔ اپلیکیشن
├── bootstrap.php         bootstrap اپلیکیشن
├── tavaan.json           مانیفست پروژه
├── .env.example          نمونهٔ تنظیمات محیطی
├── DEPENDENCIES.md       نمودار وابستگی موتورها (Mermaid)
└── README.md             این فایل
```

## معماری

```
Tavaan-Core (هستهٔ فریمورک)
   Container, Events, Autoloader, ServiceProvider, Facade, ...
       ↑
       |  Engine-Core از Container, Events, ServiceProvider استفاده می‌کند
       |
Tavaan-Engine-Core (والد موتورها)
   Engine base class + EngineBus + EngineRegistry
       ↑
       |  هر موتور از Engine ارث می‌برد
       |
۵۲ موتور فرزند
```

## سه درگاه ارتباط بین موتورها

| درگاه | الگو | کاربرد |
|---|---|---|
| **Container** | pull-based DI | `$this->make(CacheInterface::class)` |
| **Events** | pub/sub | `$this->dispatch('user.registered', $data)` |
| **EngineBus** | point-to-point RPC | `$this->call('auth', 'issue-token', $input)` |

## دستورات CLI

```bash
php bin/tvn serve      # سرور توسعه
php bin/tvn engines    # لیست موتورها
php bin/tvn info       # اطلاعات سیستم
php bin/tvn version    # نسخهٔ فریمورک
```

## آمار نسخهٔ ۰.۰.۱

- **۵۲ موتور** فرزند (هرکدام `<Name>Engine.php` دارند)
- **۲ مخزن مرکزی** (Tavaan-Core + Tavaan-Engine-Core)
- **۳ درگاه ارتباط** (Container + Events + EngineBus)
- **PHP ≥ ۸.۲** مورد‌نیاز
- **صفر‌وابستگی** Composer در زمان اجرا (autoloader خودنوشته)

## مجوز

MIT
