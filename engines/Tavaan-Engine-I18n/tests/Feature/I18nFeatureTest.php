<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n\Tests\Feature;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\I18n\Translator;
use Tavaan\Engine\I18n\I18nServiceProvider;
use Tavaan\Engine\I18n\Contracts\TranslatorInterface;

/**
 * تست‌های Feature برای موتور I18n.
 */
final class I18nFeatureTest extends TestCase
{
    public function test_service_provider_creates_translator(): void
    {
        $provider = new I18nServiceProvider(['locale' => 'fa', 'lang_dir' => __DIR__ . '/../../lang']);
        $provider->register();
        $t = $provider->getTranslator();
        $this->assertInstanceOf(Translator::class, $t);
        $this->assertInstanceOf(TranslatorInterface::class, $t);
    }

    public function test_service_provider_provides(): void
    {
        $provider = new I18nServiceProvider([]);
        $this->assertContains(TranslatorInterface::class, $provider->provides());
        $this->assertContains(Translator::class, $provider->provides());
    }

    public function test_config_file_loads(): void
    {
        $config = require __DIR__ . '/../../config/i18n.php';
        $this->assertArrayHasKey('locale', $config);
        $this->assertArrayHasKey('fallback', $config);
        $this->assertArrayHasKey('lang_dir', $config);
    }

    public function test_lang_fa_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/fa/i18n.php';
        $this->assertArrayHasKey('welcome', $messages);
        $this->assertArrayHasKey('cart', $messages);
        $this->assertArrayHasKey('items', $messages['cart']);
    }

    public function test_lang_en_loads(): void
    {
        $messages = require __DIR__ . '/../../lang/en/i18n.php';
        $this->assertArrayHasKey('welcome', $messages);
        $this->assertSame('Welcome to Tavaan', $messages['welcome']);
    }

    public function test_end_to_end_translation(): void
    {
        $provider = new I18nServiceProvider(['locale' => 'fa', 'lang_dir' => __DIR__ . '/../../lang']);
        $provider->register();
        $t = $provider->getTranslator();
        $this->assertSame('به توان خوش آمدید', $t->get('welcome'));
        // Pluralization
        $this->assertSame('سبد خالی', $t->choice('cart.items', 0));
        $this->assertSame('یک قلم', $t->choice('cart.items', 1));
        $this->assertSame('۳ قلم', $t->choice('cart.items', 3));
    }
}
