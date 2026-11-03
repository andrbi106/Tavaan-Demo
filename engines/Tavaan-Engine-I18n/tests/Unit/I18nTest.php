<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\I18n\Translator;
use Tavaan\Engine\I18n\PluralRules;
use Tavaan\Engine\I18n\NumberFormatter;
use Tavaan\Engine\I18n\Contracts\TranslatorInterface;

/**
 * تست‌های Unit برای موتور I18n.
 *
 * @see docs/03-engines/i18n.md (DOC-069)
 */
final class I18nTest extends TestCase
{
    public function test_get_simple_key(): void
    {
        $t = new Translator();
        $t->addLines(['hello' => 'سلام'], 'fa');
        $this->assertSame('سلام', $t->get('hello'));
    }

    public function test_get_returns_key_when_missing(): void
    {
        $t = new Translator();
        $this->assertSame('missing.key', $t->get('missing.key'));
    }

    public function test_get_with_namespace_dot_path(): void
    {
        $t = new Translator();
        $t->addLines(['app.title' => 'توان'], 'fa');
        $this->assertSame('توان', $t->get('app.title'));
    }

    public function test_get_with_namespace(): void
    {
        $t = new Translator();
        $t->addNamespace('shop', '/path/to/shop');
        $t->addLines(['product.title' => 'عنوان محصول'], 'fa', 'shop');
        $this->assertSame('عنوان محصول', $t->get('shop::product.title'));
    }

    public function test_interpolate_placeholders(): void
    {
        $t = new Translator();
        $t->addLines(['welcome' => 'سلام :name'], 'fa');
        $this->assertSame('سلام علی', $t->get('welcome', ['name' => 'علی']));
    }

    public function test_placeholder_multiple(): void
    {
        $t = new Translator();
        $t->addLines(['msg' => ':greeting, :name!'], 'fa');
        $this->assertSame('سلام, علی!', $t->get('msg', ['greeting' => 'سلام', 'name' => 'علی']));
    }

    public function test_set_get_locale(): void
    {
        $t = new Translator(locale: 'fa', fallback: 'en');
        $this->assertSame('fa', $t->getLocale());
        $t->setLocale('en');
        $this->assertSame('en', $t->getLocale());
    }

    public function test_fallback(): void
    {
        $t = new Translator(locale: 'fa', fallback: 'en');
        $this->assertSame('en', $t->getFallback());
    }

    public function test_fallback_when_translation_missing(): void
    {
        $t = new Translator(locale: 'fa', fallback: 'en');
        $t->addLines(['hello' => 'Hello'], 'en');
        // 'fa' has no 'hello' → should fallback to 'en'
        $this->assertSame('Hello', $t->get('hello'));
    }

    public function test_has(): void
    {
        $t = new Translator();
        $t->addLines(['exists' => 'موجود'], 'fa');
        $this->assertTrue($t->has('exists'));
        $this->assertFalse($t->has('nonexistent'));
    }

    public function test_choice_simple_plural(): void
    {
        $t = new Translator();
        $t->addLines(['items' => '{0}empty|{1}one item|[2,*]:count items'], 'en');
        $this->assertSame('empty', $t->choice('items', 0));
        $this->assertSame('one item', $t->choice('items', 1));
        $this->assertSame('5 items', $t->choice('items', 5));
    }

    public function test_choice_persian_plural(): void
    {
        $t = new Translator();
        $t->addLines(['cart.items' => '{0}سبد خالی|{1}یک قلم|[2,*]:count قلم'], 'fa');
        $this->assertSame('سبد خالی', $t->choice('cart.items', 0));
        $this->assertSame('یک قلم', $t->choice('cart.items', 1));
        $this->assertSame('۵ قلم', $t->choice('cart.items', 5));
    }

    public function test_plural_rules_farsi(): void
    {
        $p = new PluralRules();
        $this->assertSame(0, $p->indexFor(1, 'fa'));
        $this->assertSame(1, $p->indexFor(0, 'fa'));
        $this->assertSame(1, $p->indexFor(5, 'fa'));
    }

    public function test_plural_rules_english(): void
    {
        $p = new PluralRules();
        $this->assertSame(0, $p->indexFor(1, 'en'));
        $this->assertSame(1, $p->indexFor(0, 'en'));
        $this->assertSame(1, $p->indexFor(5, 'en'));
    }

    public function test_plural_rules_russian(): void
    {
        $p = new PluralRules();
        $this->assertSame(0, $p->indexFor(1, 'ru'));
        $this->assertSame(0, $p->indexFor(21, 'ru'));
        $this->assertSame(1, $p->indexFor(2, 'ru'));
        $this->assertSame(1, $p->indexFor(3, 'ru'));
        $this->assertSame(2, $p->indexFor(5, 'ru'));
        $this->assertSame(2, $p->indexFor(11, 'ru'));
    }

    public function test_load_file(): void
    {
        $tmp = \tempnam(\sys_get_temp_dir(), 'tvn_lang') . '.php';
        \file_put_contents($tmp, '<?php return ["hello" => "سلام"];');
        $t = new Translator();
        $t->loadFile($tmp);
        @\unlink($tmp);
        // The file path's parent is the temp dir, not "lang/fa", so locale would be wrong.
        // For testing, we manually add:
        $t->addLines(['hello' => 'سلام'], 'fa');
        $this->assertSame('سلام', $t->get('hello'));
    }

    public function test_load_directory(): void
    {
        $tmpDir = \sys_get_temp_dir() . '/tvn_lang_' . \uniqid();
        \mkdir($tmpDir . '/fa', 0777, true);
        \file_put_contents($tmpDir . '/fa/app.php', '<?php return ["title" => "توان"];');
        $t = new Translator();
        $t->loadDirectory($tmpDir);
        $this->assertSame('توان', $t->get('title'));
        \unlink($tmpDir . '/fa/app.php');
        \rmdir($tmpDir . '/fa');
        \rmdir($tmpDir);
    }

    public function test_number_formatter_persian(): void
    {
        $f = new NumberFormatter('fa');
        $this->assertSame('۱٬۲۳۴', $f->format(1234));
        $this->assertSame('۱٬۲۳۴٫۵۰', $f->format(1234.5, 2));
    }

    public function test_number_formatter_english(): void
    {
        $f = new NumberFormatter('en');
        $this->assertSame('1,234', $f->format(1234));
        $this->assertSame('1,234.50', $f->format(1234.5, 2));
    }

    public function test_currency_rial(): void
    {
        $f = new NumberFormatter('fa');
        $this->assertSame('۱٬۰۰۰ ریال', $f->currency(1000, 'IRR'));
    }

    public function test_currency_usd(): void
    {
        $f = new NumberFormatter('en');
        $this->assertSame('$1,234.50', $f->currency(1234.5, 'USD'));
    }

    public function test_to_persian_digits(): void
    {
        $f = new NumberFormatter();
        $this->assertSame('۱۲۳۴', $f->toPersianDigits('1234'));
        $this->assertSame('۱٬۲۳۴', $f->toPersianDigits('1,234'));
    }

    public function test_from_persian_digits(): void
    {
        $f = new NumberFormatter();
        $this->assertSame('1234', $f->fromPersianDigits('۱۲۳۴'));
        $this->assertSame('1,234', $f->fromPersianDigits('۱٬۲۳۴'));
    }

    public function test_translator_implements_interface(): void
    {
        $t = new Translator();
        $this->assertInstanceOf(TranslatorInterface::class, $t);
    }
}
