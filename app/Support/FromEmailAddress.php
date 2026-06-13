<?php

namespace App\Support;

/**
 * Build and parse Admin Console from_emails addresses (local part + domain, no extra DB column).
 */
final class FromEmailAddress
{
    public const DOMAIN_BANSAL_EDUCATION = 'bansaleducation.com.au';

    public const DOMAIN_EDUCATION_ELITE = 'educationelite.com.au';

    public const DOMAIN_BANSAL_IMMIGRATION = 'bansalimmigration.com.au';

    /**
     * @return list<string>
     */
    public static function domains(): array
    {
        return [
            self::DOMAIN_BANSAL_EDUCATION,
            self::DOMAIN_EDUCATION_ELITE,
            self::DOMAIN_BANSAL_IMMIGRATION,
        ];
    }

    public static function compose(string $local, string $domain): string
    {
        $local = self::normalizeLocal($local);
        $domain = strtolower(trim($domain));

        return $local.'@'.$domain;
    }

    /**
     * Local part only — strips @ and anything after it if pasted by mistake.
     */
    public static function normalizeLocal(string $local): string
    {
        $local = strtolower(trim($local));
        $pos = strpos($local, '@');
        if ($pos !== false) {
            $local = substr($local, 0, $pos);
        }

        return trim($local);
    }

    /**
     * @return array{local: string, domain: string}
     */
    public static function split(string $email): array
    {
        $email = strtolower(trim($email));
        $pos = strrpos($email, '@');
        if ($pos === false) {
            return ['local' => $email, 'domain' => ''];
        }

        $local = substr($email, 0, $pos);
        $host = substr($email, $pos + 1);

        foreach (self::domains() as $domain) {
            if ($host === $domain) {
                return ['local' => $local, 'domain' => $domain];
            }
        }

        return ['local' => $local, 'domain' => ''];
    }
}
