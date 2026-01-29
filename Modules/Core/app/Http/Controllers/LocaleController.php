<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Services\Locale\LocaleManager;

class LocaleController extends Controller
{
    public function __construct(
        protected LocaleManager $localeManager
    ) {
    }

    public function __invoke(Request $request, ?string $locale = null)
    {
        $targetLocale = $this->localeManager->normalize(
            $locale ?? $request->cookie($this->localeManager->cookieName())
        );

        $redirectTarget = $this->localeManager->intendedUrl($request);

        return $this->localeManager->respondWithLocale($request, $targetLocale, $redirectTarget);
    }
}
