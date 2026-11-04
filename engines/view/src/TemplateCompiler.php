<?php

declare(strict_types=1);

namespace Tavaan\Engine\View;

/**
 * TemplateCompiler — کامپایل قالب‌های `.tv.php` به PHP خالص.
 *
 * @see docs/03-engines/view.md (DOC-043)
 */
final class TemplateCompiler
{
    public function compile(string $template): string
    {
        // 1. Echo pass: {{ }} → escape, {!! !!} → raw
        $template = $this->compileEcho($template);
        // 2. Directive pass: @if/@foreach/@while/@for/...
        $template = $this->compileDirectives($template);
        // 3. Verbatim protection
        $template = $this->protectVerbatim($template);
        return $template;
    }

    private function compileEcho(string $template): string
    {
        // {!! !!} → raw
        $template = \preg_replace_callback('/\{!!\s*(.+?)\s*!!\}/', function ($m) {
            return '<?php echo ' . $m[1] . '; ?>';
        }, $template) ?? $template;
        // {{ }} → escaped
        $template = \preg_replace_callback('/\{\{\s*(.+?)\s*\}\}/', function ($m) {
            return '<?php echo \Tavaan\Engine\View\Escaper::html(' . $m[1] . '); ?>';
        }, $template) ?? $template;
        return $template;
    }

    private function compileDirectives(string $template): string
    {
        // @if, @elseif, @else, @endif
        $template = \preg_replace('/@if\s*\((.+?)\)/', '<?php if ($1): ?>', $template) ?? $template;
        $template = \preg_replace('/@elseif\s*\((.+?)\)/', '<?php elseif ($1): ?>', $template) ?? $template;
        $template = \preg_replace('/@else/', '<?php else: ?>', $template) ?? $template;
        $template = \preg_replace('/@endif/', '<?php endif; ?>', $template) ?? $template;

        // @foreach, @endforeach
        $template = \preg_replace('/@foreach\s*\((.+?)\)/', '<?php foreach ($1): ?>', $template) ?? $template;
        $template = \preg_replace('/@endforeach/', '<?php endforeach; ?>', $template) ?? $template;

        // @for, @endfor
        $template = \preg_replace('/@for\s*\((.+?)\)/', '<?php for ($1): ?>', $template) ?? $template;
        $template = \preg_replace('/@endfor/', '<?php endfor; ?>', $template) ?? $template;

        // @while, @endwhile
        $template = \preg_replace('/@while\s*\((.+?)\)/', '<?php while ($1): ?>', $template) ?? $template;
        $template = \preg_replace('/@endwhile/', '<?php endwhile; ?>', $template) ?? $template;

        // @php, @endphp
        $template = \preg_replace('/@php/', '<?php', $template) ?? $template;
        $template = \preg_replace('/@endphp/', '?>', $template) ?? $template;

        // @isset, @endisset
        $template = \preg_replace('/@isset\s*\((.+?)\)/', '<?php if (isset($1)): ?>', $template) ?? $template;
        $template = \preg_replace('/@endisset/', '<?php endif; ?>', $template) ?? $template;

        // @empty, @endempty
        $template = \preg_replace('/@empty\s*\((.+?)\)/', '<?php if (empty($1)): ?>', $template) ?? $template;
        $template = \preg_replace('/@endempty/', '<?php endif; ?>', $template) ?? $template;

        // @auth, @endauth (simplified — checks $auth variable)
        $template = \preg_replace('/@auth/', '<?php if (isset($auth) && $auth): ?>', $template) ?? $template;
        $template = \preg_replace('/@endauth/', '<?php endif; ?>', $template) ?? $template;
        $template = \preg_replace('/@guest/', '<?php if (!isset($auth) || !$auth): ?>', $template) ?? $template;
        $template = \preg_replace('/@endguest/', '<?php endif; ?>', $template) ?? $template;

        // @can, @endcan
        $template = \preg_replace('/@can\s*\((.+?)\)/', '<?php if (isset($can) && $can($1)): ?>', $template) ?? $template;
        $template = \preg_replace('/@endcan/', '<?php endif; ?>', $template) ?? $template;

        // @section, @endsection
        $template = \preg_replace('/@section\s*\((.+?)\)/', '<?php $__section = $1; ob_start(); ?>', $template) ?? $template;
        $template = \preg_replace('/@endsection/', '<?php $__sections[$__section] = ob_get_clean(); ?>', $template) ?? $template;

        // @yield
        $template = \preg_replace('/@yield\s*\((.+?)\)/', '<?php echo $__sections[$1] ?? ""; ?>', $template) ?? $template;

        // @include
        $template = \preg_replace('/@include\s*\((.+?)\)/', '<?php $__view = new \Tavaan\Engine\View\View($1, $__data ?? []); echo $__view->render(); ?>', $template) ?? $template;

        // @extends
        $template = \preg_replace('/@extends\s*\((.+?)\)/', '<?php $__extends = $1; ?>', $template) ?? $template;

        // @comment
        $template = \preg_replace('/@comment\s*\((.+?)\)/', '<?php /* $1 */ ?>', $template) ?? $template;

        return $template;
    }

    private function protectVerbatim(string $template): string
    {
        // Remove @verbatim ... @endverbatim markers (content inside is preserved as-is)
        return \preg_replace_callback('/@verbatim(.*?)@endverbatim/s', function ($m) {
            return $m[1];
        }, $template) ?? $template;
    }
}
