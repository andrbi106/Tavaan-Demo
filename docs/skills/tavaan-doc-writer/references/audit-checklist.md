# چک‌لیست ممیزی پروژه تَوان

## مرحلهٔ ۱: قوانین سخت R1-R10

### R1 — strict_types
```bash
# فایل‌های PHP بدون declare(strict_types=1):
find . -name "*.php" -not -path "*/.git/*" -not -path "*/vendor/*" | \
  xargs grep -L "declare(strict_types=1)"
```
- ✅ همه فایل‌های PHP باید `declare(strict_types=1);` داشته باشند

### R2 — بدون require/include دستی
```bash
grep -rn "require \|require_once \|include \|include_once " --include="*.php" \
  core/ engines/ app/ | grep -v "bootstrap.php" | grep -v "bin/tvn"
```
- ✅ فقط در bootstrap.php و entry pointها مجاز است

### R3 — ZeroDependency
```bash
# فایل‌های composer.json (ممنوع در core/ و engines/):
find core/ engines/ -name "composer.json"
# ارجاع به PSR:
grep -rn "use Psr\\\|extends Psr\\\\" --include="*.php" core/ engines/
# کلید psr-4:
grep -rn '"psr-4"' --include="*.php" --include="*.json" .
# require به vendor:
grep -rn "require.*vendor/" --include="*.php" core/ engines/
```
- ✅ هیچ composer.json در core/ یا engines/
- ✅ هیچ `use Psr\...`
- ✅ کلید autoload `namespace-map` (نه `psr-4`)
- ✅ هیچ require به vendor/

### R4 — بدون Property پویا
```bash
# PHP 8.2 deprecation: dynamic property
grep -rn "public \$\|protected \$\|private \$" --include="*.php" | \
  grep -v "readonly \|private readonly \|public readonly "
```
- ✅ همه propertyها typed یا readonly

### R5 — Contract-First
- ✅ هر کلاس عمومی دارای Interface است
- ✅ پیاده‌سازی پشت قرارداد

### R6 — تکمیل موتور
برای هر موتور بررسی کن:
- [ ] Provider موجود
- [ ] Interface موجود
- [ ] Tests (Unit/Feature/Benchmark) موجود
- [ ] Docs موجود
- [ ] Benchmark موجود

### R7 — Namespace ریشه
- ✅ فقط از ریشه‌های مجاز (Tavaan\Core\, Tavaan\Engine\, Tavaan\Support\, App\, Modules\, Kits\, Plugins\)

### R8 — مدیریت خطا
```bash
# استثنای خام بدون کلاس اختصاصی:
grep -rn "throw new \\\\Exception\|throw new \\\\RuntimeException\|throw new \\\\InvalidArgumentException" \
  --include="*.php" core/ engines/
```
- ✅ استثناها کلاس اختصاصی با کد عددی و MessageId دارند

### R9 — Prepared Statement
```bash
# کوئری با الحاق رشته:
grep -rn '\$.*query.*=.*".*\$' --include="*.php" engines/database/
grep -rn 'EXECUTE.*\$' --include="*.php"
```
- ✅ همه کوئری‌ها Prepared Statement یا Query Builder

### R10 — قبل از Merge
- [ ] `php -l` بدون خطا
- [ ] PHPStan level max سبز
- [ ] تست‌ها سبز
- [ ] Benchmark بدون افت

## مرحلهٔ ۲: اصول P1-P12

### P1 — Zero Dependency
(همان R3)

### P2 — Contract-First
(همان R5)

### P3 — Minimal Kernel
- ✅ هسته فقط: Autoloader, Container, Provider, Config, Env, Events, Errors, Facade, Pipeline, Contracts
- ✅ هرچه قابل‌حذف است → در موتور

### P4 — Explicit Boundaries
- ✅ ماژول‌ها فقط از درگاه عمومی هم‌کار沟通 می‌کنند
- ✅ `use Modules\Shop\Models\Order` از Blog ممنوع

### P5 — Events over Calls
- ✅ بین دامنه‌ها با رویداد، نه فراخوانی مستقیم

### P6 — Fail Fast & Loud
```bash
grep -rn "catch (\\\\Throwable)" --include="*.php" | grep -v "throw"
grep -rn "@\$" --include="*.php"
```
- ✅ بدون catch خالی، بدون @ suppression

### P7 — Secure by Default
- ✅ auto-escape در View
- ✅ Prepared Statement (R9)
- ✅ CSRF هم‌روشن
- ✅ هدرهای امنیتی پیش‌فرض

### P8 — No Hidden Magic
- ✅ Facade با @mixin
- ✅ بدون property پویا (R4)

### P9 — SRP + Small Surface
```bash
# فایل‌های طولانی:
find . -name "*.php" -not -path "*/.git/*" | while read f; do
  lines=$(wc -l < "$f")
  if [ "$lines" -gt 300 ]; then echo "$f: $lines lines"; fi
done
```
- ✅ کلاس ≤300 خط، متد ≤50 خط، ≤5 پارامتر

### P10 — Readable Performance
- ✅ بهینه‌سازی با اندازه‌گیری

### P11 — Language Forward-Compat
- ✅ بدون E_DEPRECATED روی 8.2/8.3/8.4

### P12 — Extensibility Triad
- ✅ هر موتور 3 نقطهٔ توسعه: Hook + Contract + Decorator

## مرحلهٔ ۳: ساختار پوشه

مقایسه با ساختار مرجع:
```
core/           ← هسته
engine-core/    ← والد موتورها
engines/        ← موتورهای فرزند (هر کدام: src/, config/, lang/, tests/, benchmarks/)
config/         ← فایل‌های پیکربندی
app/            ← کد اپلیکیشن
modules/        ← ماژول‌های HMVC (در صورت وجود)
routes/         ← مسیرهای وب/API/console
resources/      ← views, lang, css, js
storage/        ← logs, cache, app, framework
public/         ← entry point وب
bin/            ← CLI
```

## مرحلهٔ ۴: مستندات

برای هر موتور:
- [ ] مستند ۱۷بخشی موجود؟
- [ ] DOC-ID یکتا؟
- [ ] بلوک ماشین‌خوان YAML موجود؟
- [ ] لینک‌های داخلی معتبر؟

برای کل پروژه:
- [ ] manifest.json موجود و همگام؟
- [ ] README موجود؟
- [ ] ADRها موجود؟

## مرحلهٔ ۵: تولید گزارش

قالب گزارش:
```markdown
# گزارش ممیزی پروژه <نام>

## خلاصه
- تاریخ: YYYY-MM-DD
- نسخه پروژه: X.Y.Z
- تعداد فایل‌های PHP: N
- تعداد موتورها: N

## نتایج قوانین سخت (R1-R10)
| قانون | وضعیت | تعداد نقض | جزئیات |
|---|---|---|---|

## نتایج اصول (P1-P12)
| اصل | وضعیت | یادداشت |

## تفاوت‌ها با مرجع
| مورد | مرجع | پروژه | توصیه |

## تناقضات داخلی
| تناقض | محل | توصیه |

## توصیه‌های اولویت‌دار
1. ...
2. ...
```
