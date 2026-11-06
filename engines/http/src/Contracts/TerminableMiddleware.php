<?php

declare(strict_types=1);

namespace Tavaan\Engine\Http\Contracts;

/**
 * Middleware دارای فاز terminate (پس از ارسال پاسخ — DOC-025 P14).
 *
 * @api 1.0
 * @see docs/03-engines/http.md
 */
interface TerminableMiddleware
{
    public function terminate(RequestInterface $request, ResponseInterface $response): void;
}
