<?php
namespace App\Helpers; // Your helpers namespace 
// NOTE: User model/table has been removed
// use App\Models\User;
use App\Models\Company;
use App\Models\Profile;
use Auth;

class Helper
{
    public static function changeDateFormate($date,$date_format){
        return \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format($date_format);    
    }
    public static function getUserCompany(): ?object
    {
        $companyId = Auth::user()->comp_id ?? null;
        return $companyId ? Company::find($companyId) : null;
    }

    /**
     * Get the default CRM profile (Bansal Education Group - Profile ID 1).
     * Used for all non-invoice contexts: emails, receipts, templates, etc.
     *
     * @return \App\Models\Profile|null
     */
    public static function defaultCrmProfile(): ?Profile
    {
        $profileId = config('app.default_profile_id', 1);
        return Profile::find($profileId);
    }

    /**
     * Get the default CRM company name.
     *
     * @return string
     */
    public static function defaultCrmCompanyName(): string
    {
        $profile = self::defaultCrmProfile();
        return $profile ? $profile->company_name : 'Bansal Education Group';
    }

    /**
     * Strip cid: (Content-ID) references from email HTML to prevent ERR_UNKNOWN_URL_SCHEME.
     * Browsers cannot load cid: URLs; replace with transparent 1x1 pixel.
     * Use for server-rendered email content (Conversations tab, etc.).
     *
     * @param string|null $html
     * @return string
     */
    public static function stripCidReferences(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }
        $pixel = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
        // img src="cid:..." or src='cid:...'
        $html = preg_replace('/src=["\']cid:[^"\'>]+["\']/i', 'src="' . $pixel . '"', $html);
        // background-image: url("cid:...") or url('cid:...') or url(cid:...)
        $html = preg_replace('/background-image:\s*url\s*\(["\']?cid:[^"\'\)]+["\']?\)/i', 'background-image: none', $html);
        return $html;
    }

    /**
     * Normalize HTML fragment so unclosed/unbalanced tags don't break the page DOM.
     * Use when outputting activity or note descriptions that may contain invalid HTML.
     * Parser closes tags within the fragment so they cannot wrap following content.
     *
     * @param string $html
     * @return string
     */
    public static function normalizeActivityDescriptionHtml(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }
        $enc = 'UTF-8';
        $wrapper = '<div id="activity-desc-root">' . $html . '</div>';
        $dom = new \DOMDocument();
        @$dom->loadHTML(
            '<?xml encoding="' . $enc . '">' . $wrapper,
            \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD
        );
        $root = $dom->getElementById('activity-desc-root');
        if (!$root) {
            return $html;
        }
        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }
        return $out;
    }
}