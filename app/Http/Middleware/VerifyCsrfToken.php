<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier {
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'accounts/status',
        'api/*',
        'connect/subscribe/facebook',
        'connect/subscribe/instagram',
        'qbo/openid',
        'square/transaction/receive',
        'vault/token'
    ];
}
