<?php

namespace Modules\Core\Services\Locale;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class LocaleManager
{
    /**
     * Determine the preferred locale for the incoming request.
     *
     * @return array{locale: string, persist: bool}
     */
    public function resolve(Request $request): array
    {
        if ($this->shouldResolveFromHeader($request)) {
            $headerLocale = $this->localeFromHeader($request->header('Accept-Language'));

            if ($headerLocale !== null) {
                return ['locale' => $headerLocale, 'persist' => false];
            }
        }

        $cookieLocale = $request->cookie($this->cookieName());

        if ($this->isSupported($cookieLocale)) {
            return ['locale' => $cookieLocale, 'persist' => false];
        }

        $default = $this->default();

        return ['locale' => $default, 'persist' => true];
    }

    /**
     * Determine whether the provided locale is supported.
     */
    public function isSupported(?string $locale): bool
    {
        if ($locale === null) {
            return false;
        }

        return in_array($locale, $this->supportedKeys(), true);
    }

    /**
     * Normalize the provided locale or fall back to defaults.
     */
    public function normalize(?string $locale): string
    {
        if ($this->isSupported($locale)) {
            return $locale;
        }

        return $this->default();
    }

    public function default(): string
    {
        $default = setting('default_locale');

        if (is_string($default) && $this->isSupported($default)) {
            return $default;
        }

        return config('app.locale', 'en');
    }

    public function fallback(): string
    {
        $fallback = setting('fallback_locale');

        if (is_string($fallback) && $this->isSupported($fallback)) {
            return $fallback;
        }

        return config('app.fallback_locale', 'en');
    }

    /**
     * Build a redirect response that persists the locale in a long-lived cookie.
     */
    public function respondWithLocale(Request $request, string $locale, string $target): RedirectResponse
    {
        $normalized = $this->normalize($locale);
        $redirect = redirect()->to($this->sanitizeRedirect($request, $target));

        return $redirect->withCookie(
            cookie(
                name: $this->cookieName(),
                value: $normalized,
                minutes: $this->cookieLifetime(),
                path: '/',
                domain: config('session.domain'),
                secure: config('session.secure', false),
                httpOnly: false,
                sameSite: config('session.same_site', 'lax')
            )
        );
    }

    /**
     * Determine the target we should redirect to after switching locales.
     */
    public function intendedUrl(Request $request): string
    {
        $parameter = config('core.localization.redirect_query_key', 'redirect');
        $target = $request->query($parameter);

        if (is_string($target) && $target !== '') {
            return $target;
        }

        $previous = URL::previous();

        if ($previous && $previous !== $request->fullUrl()) {
            return $previous;
        }

        return url('/');
    }

    /**
     * All supported locales keyed by locale code.
     *
     * @return array<string, array<string, string>>
     */
    public function supported(): array
    {
        $configured = config('core.localization.supported_locales', []);

        if (! empty($configured)) {
            return $configured;
        }

        return available_locales();
    }

    /**
     * Supported locale codes.
     *
     * @return list<string>
     */
    public function supportedKeys(): array
    {
        return array_keys($this->supported());
    }

    public function cookieName(): string
    {
        return config('core.localization.cookie_name', 'gavankit_locale');
    }

    public function cookieLifetime(): int
    {
        return (int) config('core.localization.cookie_lifetime_minutes', 60 * 24 * 365 * 3);
    }

    protected function localeFromHeader(?string $header): ?string
    {
        if (! is_string($header) || trim($header) === '') {
            return null;
        }

        $candidates = explode(',', $header);

        foreach ($candidates as $candidate) {
            $normalized = strtolower(trim($candidate));

            if ($normalized === '') {
                continue;
            }

            $normalized = explode(';', $normalized, 2)[0];
            $normalized = str_replace('_', '-', $normalized);

            if ($this->isSupported($normalized)) {
                return $normalized;
            }

            if (str_contains($normalized, '-')) {
                $base = explode('-', $normalized, 2)[0];

                if ($this->isSupported($base)) {
                    return $base;
                }
            }
        }

        return null;
    }

    protected function sanitizeRedirect(Request $request, string $target): string
    {
        if (str_starts_with($target, 'http')) {
            return $target;
        }

        $cleanTarget = '/'.ltrim($target, '/');

        // Avoid redirect loops (e.g., hitting /lang repeatedly)
        if ($request->fullUrlIs(url($cleanTarget))) {
            return url('/');
        }

        return url($cleanTarget);
    }

    protected function shouldResolveFromHeader(Request $request): bool
    {
        return $request->expectsJson() || $request->wantsJson() || $request->is('api/*');
    }
}
