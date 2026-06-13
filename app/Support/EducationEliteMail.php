<?php

namespace App\Support;

/**
 * Elite / Education Elite mail domain rules: apex sender domain and subdomains
 * used for inbound mail routing (e.g. parse.example.com → mail to *@parse.example.com).
 */
final class EducationEliteMail
{
    public static function apexDomain(): string
    {
        $d = (string) config('crm.education_elite_sender_domain', 'educationelite.com.au');

        return ltrim(strtolower(trim($d)), '@');
    }

    public static function emailHost(string $email): ?string
    {
        $email = strtolower(trim($email));
        $pos = strrpos($email, '@');
        if ($pos === false) {
            return null;
        }
        $host = substr($email, $pos + 1);

        return $host !== '' ? $host : null;
    }

    /**
     * True for @apexDomain or @*.apexDomain (inbound-parse host on a subdomain).
     */
    public static function isEliteOwnedAddress(string $email): bool
    {
        $host = self::emailHost($email);
        if ($host === null) {
            return false;
        }
        $apex = self::apexDomain();
        if ($apex === '') {
            return false;
        }
        if ($host === $apex) {
            return true;
        }

        return str_ends_with($host, '.'.$apex);
    }

    /**
     * Prefer a mailbox on the apex (e.g. apply@apex) for storage / filtering when
     * forwarding also lists a parse host (e.g. inbox@parse.apex).
     *
     * @param  list<string>  $addresses  normalised lowercase addresses
     */
    public static function preferApexMailbox(array $addresses): ?string
    {
        $owned = [];
        foreach ($addresses as $addr) {
            if ($addr !== '' && self::isEliteOwnedAddress($addr)) {
                $owned[] = $addr;
            }
        }
        if ($owned === []) {
            return null;
        }
        $apex = self::apexDomain();
        foreach ($owned as $addr) {
            if (self::emailHost($addr) === $apex) {
                return $addr;
            }
        }

        return $owned[0];
    }
}
