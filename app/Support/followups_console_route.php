<?php

declare(strict_types=1);

if (! function_exists('followups_console_route')) {
    /**
     * Resolve followups.* vs adminconsole.followups.* from the current request context.
     *
     * @param  array<string, mixed>|\Illuminate\Database\Eloquent\Model|int|string  $parameters
     */
    function followups_console_route(string $suffix, mixed $parameters = []): string
    {
        $prefix = request()->routeIs('adminconsole.followups.*') ? 'adminconsole.followups.' : 'followups.';

        return route($prefix.$suffix, $parameters);
    }
}
