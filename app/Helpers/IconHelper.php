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
 */
class IconHelper
{
    private const MODIFIER_CLASSES = [
        'fa-spin', 'fa-pulse', 'fa-fw', 'icon-spin', 'icon-fw',
        'fa-xs', 'fa-sm', 'fa-lg', 'fa-1x', 'fa-2x', 'fa-3x',
        'icon-xs', 'icon-sm', 'icon-lg', 'icon-1x', 'icon-2x', 'icon-3x',
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

        $faSlug = self::normalizeFaSlug($name);

        return self::buildLucideTag($faSlug, self::normalizeStyle($style), $options);
    }

    public static function classes(string $name, string $style = 'solid', array $options = []): string
    {
        if (self::looksLikeClassString($name)) {
            return self::parseClassString($name, $options);
        }

        $faSlug = self::normalizeFaSlug($name);

        return implode(' ', self::buildLucideClassList($faSlug, self::normalizeStyle($style), $options));
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
        $faSlug = self::nameFromClassString($classString) ?? 'tag';
        $style = self::styleFromClassString($classString);

        return self::buildLucideTag($faSlug, $style, $options, $classString);
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
            'name' => self::normalizeFaSlug($stored),
            'style' => 'solid',
        ];
    }

    public static function lucideName(string $faName): string
    {
        $faName = self::normalizeFaSlug($faName);
        $map = config('icons.fa_to_lucide', []);

        return $map[$faName] ?? str_replace('_', '-', $faName);
    }

    /** @return array<string, string> */
    public static function faToLucideMap(): array
    {
        return config('icons.fa_to_lucide', []);
    }

    private static function looksLikeClassString(string $value): bool
    {
        return (bool) preg_match('/\bfa[srb]?\s+fa-|\bfa-solid\s+fa-|\bfa-regular\s+fa-|\bfa-brands\s+fa-/', $value);
    }

    private static function normalizeStyle(string $style): string
    {
        $style = strtolower($style);

        return match ($style) {
            'far', 'fa-regular', 'regular' => 'regular',
            'fab', 'fa-brands', 'brands' => 'brands',
            default => 'solid',
        };
    }

    private static function normalizeFaSlug(string $name): string
    {
        $name = trim($name);

        if (str_starts_with($name, 'fa-')) {
            return substr($name, 3);
        }

        return $name;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private static function buildLucideTag(string $faSlug, string $style, array $options, ?string $legacyClassString = null): string
    {
        if ($style === 'brands' && $faSlug === 'google') {
            return self::buildGoogleBrandTag($options);
        }

        $classes = self::buildLucideClassList($faSlug, $style, $options, $legacyClassString);
        $attributes = [
            'class' => implode(' ', $classes),
            'data-lucide' => self::lucideName($faSlug),
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

    /**
     * @param  array<string, mixed>  $options
     * @return list<string>
     */
    private static function buildLucideClassList(string $faSlug, string $style, array $options, ?string $legacyClassString = null): array
    {
        $classes = ['crm-icon'];

        if ($style === 'regular') {
            $classes[] = 'crm-icon-regular';
        }

        if (! empty($options['spin']) || ($faSlug === 'spinner')) {
            $classes[] = 'icon-spin';
        }

        if (! empty($options['size'])) {
            $classes[] = self::sizeClass((string) $options['size']);
        }

        if ($legacyClassString) {
            foreach (preg_split('/\s+/', trim($legacyClassString)) ?: [] as $part) {
                if ($part === '' || in_array($part, ['fas', 'far', 'fab', 'fa-solid', 'fa-regular', 'fa-brands'], true)) {
                    continue;
                }
                if (str_starts_with($part, 'fa-') && ! in_array($part, self::MODIFIER_CLASSES, true) && $part !== 'fa-' . $faSlug) {
                    continue;
                }
                if (in_array($part, self::MODIFIER_CLASSES, true)) {
                    $classes[] = self::sizeClass($part);
                }
            }
        }

        if (! empty($options['class'])) {
            foreach (preg_split('/\s+/', trim((string) $options['class'])) ?: [] as $extra) {
                if ($extra !== '') {
                    $classes[] = $extra;
                }
            }
        }

        return array_values(array_unique(array_filter($classes)));
    }

    private static function sizeClass(string $size): string
    {
        $map = config('icons.size_classes', []);

        return $map[$size] ?? (str_starts_with($size, 'icon-') ? $size : 'icon-' . (str_starts_with($size, 'fa-') ? substr($size, 3) : $size));
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private static function parseClassString(string $classString, array $options = []): string
    {
        $faSlug = self::nameFromClassString($classString) ?? 'tag';
        $style = self::styleFromClassString($classString);

        return implode(' ', self::buildLucideClassList($faSlug, $style, $options, $classString));
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private static function buildGoogleBrandTag(array $options): string
    {
        $classes = self::buildLucideClassList('google', 'brands', $options);
        $classes[] = 'crm-icon-brand';
        $classes[] = 'crm-icon-google';

        if (! empty($options['attrs']) && is_array($options['attrs']) && ! empty($options['attrs']['class'])) {
            foreach (preg_split('/\s+/', trim((string) $options['attrs']['class'])) ?: [] as $extra) {
                if ($extra !== '') {
                    $classes[] = $extra;
                }
            }
        }

        $classAttr = implode(' ', array_values(array_unique($classes)));

        return '<span class="' . e($classAttr) . '" aria-hidden="true">'
            . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true">'
            . '<path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>'
            . '<path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>'
            . '<path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>'
            . '<path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>'
            . '</svg></span>';
    }
}
