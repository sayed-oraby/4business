<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Core\Services\Locale\LocaleManager;

class ResolveLocale
{
    public function __construct(
        protected LocaleManager $localeManager
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        $resolution = $this->localeManager->resolve($request);
        $locale = $resolution['locale'];

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        $response = $next($request);

        if ($resolution['persist']) {
            $response->headers->setCookie(
                cookie(
                    name: $this->localeManager->cookieName(),
                    value: $locale,
                    minutes: $this->localeManager->cookieLifetime(),
                    path: '/',
                    domain: config('session.domain'),
                    secure: config('session.secure', false),
                    httpOnly: false,
                    sameSite: config('session.same_site', 'lax')
                )
            );
        }

        return $response;
    }
}
