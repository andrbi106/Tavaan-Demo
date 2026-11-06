<?php

declare(strict_types=1);

namespace Tavaan\Engine\Admin;

/**
 * Form — renders HTML form fields with consistent admin styling.
 */
final class Form
{
    private string $name;
    /** @var array<string, mixed> */
    private array $values = [];

    public function __construct(string $name = 'form')
    {
        $this->name = $name;
    }

    public function setValues(array $values): self
    {
        $this->values = $values;
        return $this;
    }

    private function val(string $field): string
    {
        $v = $this->values[$field] ?? '';
        return is_scalar($v) ? htmlspecialchars((string) $v) : '';
    }

    /**
     * text — single-line text input.
     */
    public function text(string $name, ?string $label = null, array $attrs = []): string
    {
        $id = $this->fieldId($name);
        $attrStr = $this->renderAttrs($attrs);
        $html = '<div class="form-group">';
        $html .= '<label for="' . $id . '">' . htmlspecialchars($label ?? ucfirst($name)) . '</label>';
        $html .= '<input type="text" id="' . $id . '" name="' . $name . '" value="' . $this->val($name) . '"' . $attrStr . '>';
        $html .= '</div>';
        return $html;
    }

    /**
     * textarea — multi-line text input.
     */
    public function textarea(string $name, ?string $label = null, array $attrs = []): string
    {
        $id = $this->fieldId($name);
        $attrStr = $this->renderAttrs($attrs);
        $html = '<div class="form-group">';
        $html .= '<label for="' . $id . '">' . htmlspecialchars($label ?? ucfirst($name)) . '</label>';
        $html .= '<textarea id="' . $id . '" name="' . $name . '"' . $attrStr . '>' . $this->val($name) . '</textarea>';
        $html .= '</div>';
        return $html;
    }

    /**
     * number — numeric input.
     */
    public function number(string $name, ?string $label = null, array $attrs = []): string
    {
        $id = $this->fieldId($name);
        $attrStr = $this->renderAttrs($attrs);
        $html = '<div class="form-group">';
        $html .= '<label for="' . $id . '">' . htmlspecialchars($label ?? ucfirst($name)) . '</label>';
        $html .= '<input type="number" id="' . $id . '" name="' . $name . '" value="' . $this->val($name) . '"' . $attrStr . '>';
        $html .= '</div>';
        return $html;
    }

    /**
     * money — currency-formatted number input (in Toman by default).
     */
    public function money(string $name, ?string $label = null, array $attrs = []): string
    {
        $attrs['step'] = $attrs['step'] ?? '0.01';
        $html = '<div class="form-group">';
        $html .= '<label for="' . $this->fieldId($name) . '">' . htmlspecialchars($label ?? ucfirst($name)) . ' (تومان)</label>';
        $html .= '<div class="input-money">' . $this->number($name, null, $attrs) . '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * select — dropdown input.
     * @param array<string, string> $options
     */
    public function select(string $name, array $options = [], ?string $label = null, array $attrs = []): string
    {
        $id = $this->fieldId($name);
        $attrStr = $this->renderAttrs($attrs);
        $html = '<div class="form-group">';
        $html .= '<label for="' . $id . '">' . htmlspecialchars($label ?? ucfirst($name)) . '</label>';
        $html .= '<select id="' . $id . '" name="' . $name . '"' . $attrStr . '>';
        $current = $this->val($name);
        foreach ($options as $value => $text) {
            $sel = (string) $value === $current ? ' selected' : '';
            $html .= '<option value="' . htmlspecialchars((string) $value) . '"' . $sel . '>' . htmlspecialchars($text) . '</option>';
        }
        $html .= '</select></div>';
        return $html;
    }

    /**
     * relation — relational dropdown (for foreign keys).
     * @param iterable<array{id?: mixed, title?: string}> $items
     */
    public function relation(string $name, iterable $items, ?string $label = null, array $attrs = []): string
    {
        $options = [];
        foreach ($items as $item) {
            $id = (string) ($item['id'] ?? '');
            $title = (string) ($item['title'] ?? (string) $id);
            $options[$id] = $title;
        }
        return $this->select($name, $options, $label, $attrs);
    }

    /**
     * media — file upload input with preview.
     */
    public function media(string $name, ?string $label = null, array $attrs = []): string
    {
        $id = $this->fieldId($name);
        $attrStr = $this->renderAttrs($attrs);
        $html = '<div class="form-group form-media">';
        $html .= '<label for="' . $id . '">' . htmlspecialchars($label ?? ucfirst($name)) . '</label>';
        $html .= '<input type="file" id="' . $id . '" name="' . $name . '"' . $attrStr . '>';
        if ($this->val($name)) {
            $html .= '<img src="' . $this->val($name) . '" alt="" class="media-preview">';
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * richtext — textarea with a WYSIWYG marker (admin JS attaches editor).
     */
    public function richtext(string $name, ?string $label = null, array $attrs = []): string
    {
        $attrs['class'] = ($attrs['class'] ?? '') . ' richtext';
        $html = $this->textarea($name, $label, $attrs);
        return str_replace('<textarea', '<textarea data-richtext="true"', $html);
    }

    /**
     * jalaliDate — Jalali date picker input.
     */
    public function jalaliDate(string $name, ?string $label = null, array $attrs = []): string
    {
        $id = $this->fieldId($name);
        $attrs['class'] = ($attrs['class'] ?? '') . ' jalali-datepicker';
        $attrStr = $this->renderAttrs($attrs);
        $html = '<div class="form-group form-jalali">';
        $html .= '<label for="' . $id . '">' . htmlspecialchars($label ?? ucfirst($name)) . '</label>';
        $html .= '<input type="text" id="' . $id . '" name="' . $name . '" value="' . $this->val($name) . '" placeholder="YYYY/MM/DD"' . $attrStr . '>';
        $html .= '</div>';
        return $html;
    }

    /**
     * repeater — repeated field group with add/remove buttons.
     * @param list<array<string, mixed>> $items
     */
    public function repeater(string $name, array $items = [], ?string $label = null, array $fieldTemplate = []): string
    {
        $html = '<div class="form-group repeater" data-repeater="' . $name . '">';
        $html .= '<label>' . htmlspecialchars($label ?? ucfirst($name)) . '</label>';
        $html .= '<div class="repeater-items">';
        foreach ($items as $item) {
            $html .= '<div class="repeater-item">';
            foreach ($fieldTemplate as $field) {
                $html .= '<span>' . htmlspecialchars((string) ($item[$field] ?? '')) . '</span>';
            }
            $html .= ' <button type="button" class="repeater-remove">حذف</button></div>';
        }
        $html .= '</div>';
        $html .= '<button type="button" class="repeater-add">افزودن</button>';
        $html .= '</div>';
        return $html;
    }

    private function fieldId(string $field): string
    {
        return 'field_' . str_replace(['[', ']'], ['_', ''], $field);
    }

    private function renderAttrs(array $attrs): string
    {
        $out = '';
        foreach ($attrs as $k => $v) {
            if ($v === true) {
                $out .= ' ' . $k;
            } elseif ($v !== false) {
                $out .= ' ' . $k . '="' . htmlspecialchars((string) $v) . '"';
            }
        }
        return $out;
    }
}
