<?php

namespace Modules\Activity\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Modules\Activity\Models\AuditLog;
use Modules\Activity\Services\NotificationPublisher;

class AuditLogger
{
    public function __construct(
        protected Request $request,
        protected NotificationPublisher $notifications
    ) {
    }

    public function log(?int $userId, string $action, ?string $description = null, array $properties = [], ?string $guard = null, ?string $ip = null, ?string $userAgent = null): AuditLog
    {
        $ip ??= $this->request->ip();
        $userAgent ??= $this->request->userAgent();
        $device = $properties['device'] ?? $this->request->header('X-Device');
        $context = $properties['context'] ?? null;

        $actionLabel = $this->translateAction($action);
        $contextLabel = $this->translateContext($context);

        $descriptionKey = $properties['description_key'] ?? null;
        $descriptionParams = $properties['description_params'] ?? [];
        $notificationMessageKey = $properties['notification_message_key'] ?? null;
        $notificationMessageParams = $properties['notification_message_params'] ?? [];

        $description ??= $actionLabel;

        $properties = array_merge($properties, [
            'description_key' => $descriptionKey,
            'description_params' => $descriptionParams,
            'notification_message_key' => $notificationMessageKey,
            'notification_message_params' => $notificationMessageParams,
        ]);

        $log = AuditLog::create([
            'user_id' => $userId,
            'guard' => $guard ?? config('auth.defaults.guard'),
            'action' => $action,
            'description' => $description,
            'context' => $context,
            'properties' => $properties,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'device' => $device,
            'platform' => $properties['platform'] ?? null,
            'browser' => $properties['browser'] ?? null,
            'occurred_at' => now(),
        ]);

        if (($properties['notify'] ?? true) === true) {
            $payload = [
                'action' => $action,
                'action_label' => $actionLabel,
                'context' => $log->context,
                'context_label' => $contextLabel,
                'ip' => $log->ip_address,
                'device' => $log->device,
                'user' => $log->user?->only(['id', 'name', 'email']),
                'title_key' => $properties['title_key'] ?? $descriptionKey ?? $this->actionKey($action),
                'title_params' => $properties['title_params'] ?? $descriptionParams,
                'message_key' => $notificationMessageKey,
                'message_params' => $notificationMessageParams,
            ];

            $payload['title_translations'] = $payload['title_translations']
                ?? $this->buildTranslations(
                    $payload['title_key'],
                    $payload['title_params'],
                    $description ?? $actionLabel
                );

            $payload['message_translations'] = $payload['message_translations']
                ?? $this->buildTranslations(
                    $payload['message_key'],
                    $payload['message_params'],
                    $properties['notification_message'] ?? null
                );

            $this->notifications->publish([
                'type' => $properties['notification_type'] ?? 'audit',
                'level' => $properties['level'] ?? 'info',
                'title' => $description ?? $actionLabel,
                'message' => $properties['notification_message'] ?? null,
                'payload' => $payload,
            ]);
        }

        return $log;
    }

    protected function translateAction(string $action): string
    {
        $key = $this->actionKey($action);

        return Lang::has($key) ? __($key) : $action;
    }

    protected function actionKey(string $action): string
    {
        return "dashboard.actions_map.$action";
    }

    protected function translateContext(?string $context): ?string
    {
        if (empty($context)) {
            return null;
        }

        $key = "dashboard.contexts.$context";

        return Lang::has($key) ? __($key) : $context;
    }

    protected function buildTranslations(?string $key, array $params = [], ?string $fallback = null): ?array
    {
        if (! $key) {
            return $fallback ? [$this->defaultLocale() => $fallback] : null;
        }

        $locales = $this->supportedLocales();
        $translations = [];

        foreach ($locales as $locale) {
            if (! Lang::has($key, $locale)) {
                continue;
            }

            $translations[$locale] = Lang::get($key, $params, $locale);
        }

        if (empty($translations) && $fallback) {
            $translations[$this->defaultLocale()] = $fallback;
        }

        return $translations ?: null;
    }

    protected function supportedLocales(): array
    {
        $configured = config('core.localization.supported_locales', []);

        if (! empty($configured)) {
            return array_keys($configured);
        }

        return array_keys(available_locales());
    }

    protected function defaultLocale(): string
    {
        return config('app.locale', 'en');
    }
}
