<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Setting\Http\Requests\UpdateSettingsRequest;
use Modules\Setting\Services\SettingsService;

class SettingController extends Controller
{
    public function __construct(protected SettingsService $service)
    {
    }

    public function index()
    {
        return view('setting::index', $this->service->screenData());
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $this->service->update($request);

        return redirect()
            ->route('dashboard.settings.index')
            ->with('success', __('setting::settings.messages.saved'));
    }
}
