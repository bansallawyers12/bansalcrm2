<?php

namespace App\Helpers;

class IconHelper
{
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

    /**
     * Render a Font Awesome icon element.
     *
     * @param  string  $name   Icon name (inbox) or legacy class string (fas fa-inbox)
     * @param  string  $style  solid|regular|brands (or fas|far|fab)
     * @param  array<string, mixed>  $options  class, spin, size (lg|2x|3x), attrs
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

    /**
     * Build only the class attribute value (for controllers / JS templates).
     */
    public static function classes(string $name, string $style = 'solid', array $options = []): string
    {
        if (self::looksLikeClassString($name)) {
            return self::parseClassString($name, $options);
        }

        $prefix = self::stylePrefix($style);
        $iconName = self::normalizeIconName($name);

        return implode(' ', self::buildClassList($prefix, $iconName, $options));
    }

    public static function renderFromClassString(string $classString, array $options = []): string
    {
        $classes = self::parseClassString($classString, $options);

        return self::buildTag(explode(' ', $classes), $options);
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
