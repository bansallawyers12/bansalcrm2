<?php
namespace App\Helpers;

use Illuminate\Support\Str;

class UserAgentParser
{
    /**
     * Parse raw user agent string to human-readable browser + OS.
     *
     * @param string|null $ua
     * @return string e.g. "Chrome on Windows" or "—"
     */
    public static function parse(?string $ua): string
    {
        if (empty($ua) || trim($ua) === '') {
            return '—';
        }

        $browser = self::extractBrowser($ua);
        $os = self::extractOs($ua);

        if ($browser && $os) {
            return $browser . ' on ' . $os;
        }
        if ($browser) {
            return $browser;
        }
        if ($os) {
            return $os;
        }

        return Str::limit(trim($ua), 40);
    }

    protected static function extractBrowser(string $ua): ?string
    {
        if (preg_match('/Edg\/([\d.]+)/', $ua, $m)) {
            return 'Edge ' . explode('.', $m[1])[0];
        }
        if (preg_match('/Chrome\/([\d.]+)/', $ua, $m) && !preg_match('/Chromium|Edg/', $ua)) {
            return 'Chrome ' . explode('.', $m[1])[0];
        }
        if (preg_match('/Firefox\/([\d.]+)/', $ua, $m)) {
            return 'Firefox ' . explode('.', $m[1])[0];
        }
        if (preg_match('/Safari\/([\d.]+)/', $ua, $m) && !preg_match('/Chrome/', $ua)) {
            return 'Safari ' . explode('.', $m[1])[0];
        }
        if (preg_match('/OPR\/([\d.]+)/', $ua, $m)) {
            return 'Opera ' . explode('.', $m[1])[0];
        }
        if (preg_match('/MSIE ([\d.]+)/', $ua, $m) || preg_match('/Trident\/.*rv:([\d.]+)/', $ua, $m)) {
            return 'IE ' . explode('.', $m[1])[0];
        }

        return null;
    }

    protected static function extractOs(string $ua): ?string
    {
        if (preg_match('/Windows NT 10/', $ua)) {
            return 'Windows';
        }
        if (preg_match('/Windows NT 6\.\d/', $ua)) {
            return 'Windows';
        }
        if (preg_match('/Mac OS X ([^)]+)/', $ua, $m)) {
            return 'macOS ' . str_replace('_', '.', substr($m[1], 0, 5));
        }
        if (preg_match('/iPhone|iPad/', $ua)) {
            return 'iOS';
        }
        if (preg_match('/Android/', $ua)) {
            return 'Android';
        }
        if (preg_match('/Linux/', $ua)) {
            return 'Linux';
        }

        return null;
    }
}
