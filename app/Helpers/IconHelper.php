<?php

namespace App\Helpers;

/**
 * Central icon renderer for Blade, PHP, and (via crm-icon.js) JavaScript.
 *
 * Usage:
 *   Blade:  @icon('inbox')  @icon('envelope', 'regular')  @icon('spinner', 'solid', ['spin' => true])
 *   PHP:    IconHelper::render('trash')
 *   Stored: IconHelper::renderStored($label->icon)  // accepts "inbox" or legacy "fas fa-inbox"
 *
 * Options: spin (bool), size (lg|2x|3x), class (extra classes), attrs (HTML attributes).
 * Styles: solid (default), regular, brands — or fas|far|fab.
 *
 * Lucide: swap render() internals only; callers stay unchanged.
 */
class IconHelper
{
    /** @var array<string, string> Future Lucide name map (FA slug => Lucide slug). */
    public const FA_TO_LUCIDE = [
        // 'sign-out-alt' => 'log-out',
    ];

    private const STYLE_PREFIXES = [
        'solid' => 'fas',
        'fas' => 'fas',
        'fa-solid' => 'fas',
        'regular' => 'far',
        'far' => 'far',
        'fa-regular' => 'far',
        'brands' => 'fab',
        'fab' => 'fab',
        'fa-brands' => 'fab',
    ];

    private const MODIFIER_CLASSES = [
        'fa-spin', 'fa-pulse', 'fa-fw',
        'fa-xs', 'fa-sm', 'fa-lg', 'fa-1x', 'fa-2x', 'fa-3x', 'fa-4x', 'fa-5x',
        'fa-6x', 'fa-7x', 'fa-8x', 'fa-9x', 'fa-10x',
    ];

    /**
     * @param  string  $name   Icon name (inbox) or legacy class string (fas fa-inbox)
     * @param  string  $style  solid|regular|brands (or fas|far|fab)
     * @param  array<string, mixed>  $options
     */
    public static function render(string $name, string $style = 'solid', array $options = []): string
    {
        if (self::looksLikeClassString($name)) {
            return self::renderFromClassString($name, $options);
        }

        $prefix = self::stylePrefix($style);
        $iconName = self::normalizeIconName($name);
        $classes = self::buildClassList($prefix, $iconName, $options);

        return self::buildTag($classes, $options);
    }

    public static function classes(string $name, string $style = 'solid', array $options = []): string
    {
        if (self::looksLikeClassString($name)) {
            return self::parseClassString($name, $options);
        }

        $prefix = self::stylePrefix($style);
        $iconName = self::normalizeIconName($name);

        return implode(' ', self::buildClassList($prefix, $iconName, $options));
    }

    /** Render icon from DB/config value ("inbox" or legacy "fas fa-inbox"). */
    public static function renderStored(?string $stored, array $options = [], string $defaultName = 'tag'): string
    {
        $stored = trim((string) $stored);

        if ($stored === '') {
            return self::render($defaultName, 'solid', $options);
        }

        if (self::looksLikeClassString($stored)) {
            return self::renderFromClassString($stored, $options);
        }

        return self::render($stored, 'solid', $options);
    }

    /** Class string from stored icon value. */
    public static function classesFromStored(?string $stored, array $options = [], string $defaultName = 'tag'): string
    {
        $stored = trim((string) $stored);

        if ($stored === '') {
            return self::classes($defaultName, 'solid', $options);
        }

        if (self::looksLikeClassString($stored)) {
            return self::parseClassString($stored, $options);
        }

        return self::classes($stored, 'solid', $options);
    }

    public static function renderFromClassString(string $classString, array $options = []): string
    {
        $classes = self::parseClassString($classString, $options);

        return self::buildTag(explode(' ', $classes), $options);
    }

    /** Extract icon slug from "fas fa-inbox" → "inbox". */
    public static function nameFromClassString(string $classString): ?string
    {
        foreach (preg_split('/\s+/', trim($classString)) ?: [] as $part) {
            if (! str_starts_with($part, 'fa-')) {
                continue;
            }

            if (in_array($part, self::MODIFIER_CLASSES, true)) {
                continue;
            }

            return substr($part, 3);
        }

        return null;
    }

    /** Extract style from class string → solid|regular|brands. */
    public static function styleFromClassString(string $classString): string
    {
        if (preg_match('/\b(far|fa-regular)\b/', $classString)) {
            return 'regular';
        }

        if (preg_match('/\b(fab|fa-brands)\b/', $classString)) {
            return 'brands';
        }

        return 'solid';
    }

    /**
     * @return array{name: string, style: string}
     */
    public static function parseStored(?string $stored, string $defaultName = 'tag'): array
    {
        $stored = trim((string) $stored);

        if ($stored === '') {
            return ['name' => $defaultName, 'style' => 'solid'];
        }

        if (self::looksLikeClassString($stored)) {
            return [
                'name' => self::nameFromClassString($stored) ?? $defaultName,
                'style' => self::styleFromClassString($stored),
            ];
        }

        return [
            'name' => ltrim($stored, 'fa-'),
            'style' => 'solid',
        ];
    }

    public static function lucideName(string $faName): string
    {
        $faName = ltrim($faName, 'fa-');

        return self::FA_TO_LUCIDE[$faName] ?? $faName;
    }

    private static function looksLikeClassString(string $value): bool
    {
        return (bool) preg_match('/\bfa[srb]?\s+fa-|\bfa-solid\s+fa-|\bfa-regular\s+fa-|\bfa-brands\s+fa-/', $value);
    }

    private static function stylePrefix(string $style): string
    {
        return self::STYLE_PREFIXES[strtolower($style)] ?? 'fas';
    }

    private static function normalizeIconName(string $name): string
    {
        $name = trim($name);

        if (str_starts_with($name, 'fa-')) {
            return $name;
        }

        return 'fa-' . $name;
    }

    /**
     * @param  array<string, mixed>  $options
     * @return list<string>
     */
    private static function buildClassList(string $prefix, string $iconName, array $options): array
    {
        $classes = [$prefix, $iconName];

        if (! empty($options['spin'])) {
            $classes[] = 'fa-spin';
            $classes[] = 'icon-spin';
        }

        if (! empty($options['size'])) {
            $size = (string) $options['size'];
            $classes[] = str_starts_with($size, 'fa-') ? $size : 'fa-' . $size;
        }

        if (! empty($options['class'])) {
            foreach (preg_split('/\s+/', trim((string) $options['class'])) ?: [] as $extra) {
                if ($extra !== '') {
                    $classes[] = $extra;
                }
            }
        }

        return array_values(array_unique($classes));
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private static function parseClassString(string $classString, array $options = []): string
    {
        $classes = preg_split('/\s+/', trim($classString)) ?: [];

        if (! empty($options['spin'])) {
            $classes[] = 'fa-spin';
            $classes[] = 'icon-spin';
        }

        if (! empty($options['size'])) {
            $size = (string) $options['size'];
            $classes[] = str_starts_with($size, 'fa-') ? $size : 'fa-' . $size;
        }

        if (! empty($options['class'])) {
            foreach (preg_split('/\s+/', trim((string) $options['class'])) ?: [] as $extra) {
                if ($extra !== '') {
                    $classes[] = $extra;
                }
            }
        }

        return implode(' ', array_values(array_unique(array_filter($classes))));
    }

    /**
     * @param  list<string>  $classes
     * @param  array<string, mixed>  $options
     */
    private static function buildTag(array $classes, array $options = []): string
    {
        $attributes = [
            'class' => implode(' ', $classes),
            'aria-hidden' => 'true',
        ];

        if (! empty($options['attrs']) && is_array($options['attrs'])) {
            if (isset($options['attrs']['class']) && $options['attrs']['class'] !== '') {
                $attributes['class'] = trim($attributes['class'] . ' ' . $options['attrs']['class']);
            }

            foreach ($options['attrs'] as $key => $value) {
                if ($key === 'class' || $value === null || $value === '') {
                    continue;
                }
                $attributes[$key] = $value;
            }
        }

        $html = '<i';
        foreach ($attributes as $key => $value) {
            $html .= ' ' . $key . '="' . e($value) . '"';
        }

        return $html . '></i>';
    }
}
