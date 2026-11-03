<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n;

use Tavaan\Engine\I18n\Contracts\TranslatorInterface;

/**
 * I18nServiceProvider — ثبت موتور I18n.
 */
final class I18nServiceProvider
{
    private ?Translator $translator = null;
    private ?NumberFormatter $formatter = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly array $config = [],
    ) {
    }

    public function register(): void
    {
        $this->translator = new Translator(
            locale: $this->config['locale'] ?? 'fa',
            fallback: $this->config['fallback'] ?? 'en',
        );
        $langDir = $this->config['lang_dir'] ?? 'lang';
        $this->translator->loadDirectory($langDir);
        $this->formatter = new NumberFormatter($this->config['locale'] ?? 'fa');
    }

    public function boot(): void
    {
        // No-op
    }

    /**
     * @return list<class-string>
     */
    public function provides(): array
    {
        return [TranslatorInterface::class, Translator::class, NumberFormatter::class];
    }

    public function getTranslator(): Translator
    {
        if ($this->translator === null) {
            $this->register();
        }
        return $this->translator;
    }

    public function getFormatter(): NumberFormatter
    {
        if ($this->formatter === null) {
            $this->register();
        }
        return $this->formatter;
    }
}
