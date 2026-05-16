<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

class VerifyCsrfToken extends PreventRequestForgery
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * Extra URIs excluded only when this class is used directly (e.g. Sanctum SPA stack).
     * Primary exclusions live in {@see bootstrap/app.php} via preventRequestForgery(), which
     * registers paths on PreventRequestForgery statically and they apply here too.
     *
     * @var array<int, string>
     */
    protected $except = [];
}
