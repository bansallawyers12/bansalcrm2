<?php

namespace App\Support;

use App\Models\EliteEmailAttachment;
use Illuminate\Support\Collection;

class EliteEmailCidRewriter
{
    /**
     * Replace cid: references in HTML with absolute attachment URLs.
     *
     * @param  Collection<int, EliteEmailAttachment>  $attachments
     */
    public static function rewrite(string $html, Collection $attachments): string
    {
        if ($html === '' || $attachments->isEmpty() || ! str_contains(strtolower($html), 'cid:')) {
            return $html;
        }

        $map = [];
        foreach ($attachments as $a) {
            if (! $a->content_id) {
                continue;
            }
            $cid = self::normalizeCid((string) $a->content_id);
            if ($cid !== '') {
                $map[$cid] = route('elite.emails.attachment', ['attachment' => $a->id], true);
            }
        }

        if ($map === []) {
            $firstImage = $attachments->first(static function (EliteEmailAttachment $a) {
                return str_starts_with(strtolower((string) ($a->mime_type ?? '')), 'image/');
            });
            if ($firstImage !== null) {
                $url = route('elite.emails.attachment', ['attachment' => $firstImage->id], true);
                $html = preg_replace('/\bsrc=(["\']?)cid:[^"\'>\s]+\1/i', 'src=$1'.$url.'$1', $html, 1) ?? $html;
                $html = preg_replace(
                    '#background-image\s*:\s*url\(\s*[\'"]?cid:[^\'")\s]+[\'"]?\s*\)#i',
                    'background-image: url("'.$url.'")',
                    $html,
                    1
                ) ?? $html;
            }

            return $html;
        }

        $html = preg_replace_callback(
            '/\bsrc=(["\']?)cid:([^"\'>\s]+)\1/i',
            static function (array $m) use ($map) {
                $key = self::normalizeCid($m[2]);
                $url = $map[$key] ?? $map[self::cidLocalPart($key)] ?? '';

                return $url !== '' ? 'src='.$m[1].$url.$m[1] : $m[0];
            },
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '#background-image\s*:\s*url\(\s*[\'"]?cid:([^\'")\s]+)[\'"]?\s*\)#i',
            static function (array $m) use ($map) {
                $key = self::normalizeCid($m[1]);
                $url = $map[$key] ?? $map[self::cidLocalPart($key)] ?? '';
                if ($url === '') {
                    return $m[0];
                }

                return 'background-image: url("'.$url.'")';
            },
            $html
        ) ?? $html;

        return $html;
    }

    public static function normalizeCid(string $cid): string
    {
        $cid = strtolower(trim($cid));

        return trim($cid, '<>');
    }

    private static function cidLocalPart(string $normalized): string
    {
        $p = explode('@', $normalized, 2);

        return $p[0] ?? $normalized;
    }
}
