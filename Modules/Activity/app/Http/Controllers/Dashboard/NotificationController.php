<?php

namespace Modules\Activity\Http\Controllers\Dashboard;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Modules\Activity\Models\SystemNotification;

class NotificationController extends Controller
{
    public function feed(): JsonResponse
    {
        $notifications = SystemNotification::query()
            ->with('user')
            ->latest()
            ->limit(30)
            ->get();

        $sections = [
            'notifications' => [],
            'logs' => [],
        ];

        foreach ($notifications as $notification) {
            $bucket = $notification->category === 'logs' ? 'logs' : 'notifications';
            $sections[$bucket][] = $this->present($notification);
        }

        $categoryCounts = $this->categoryCounts();
        $totalNotifications = array_sum(array_column($categoryCounts, 'total'));
        $unreadForBadge = $categoryCounts['notifications']['unread'];

        return response()->json([
            'sections' => $sections,
            'counts' => [
                'notifications' => $categoryCounts['notifications'],
                'logs' => $categoryCounts['logs'],
                'total' => $totalNotifications,
                'unread' => $unreadForBadge,
            ],
        ]);
    }

    public function markAll(): JsonResponse
    {
        SystemNotification::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function importantData(Request $request): JsonResponse
    {
        $baseQuery = SystemNotification::query()
            ->with('user')
            ->whereNotIn('type', ['log', 'audit'])
            ->orderBy('created_at', 'desc');

        $query = clone $baseQuery;

        $search = $request->input('search.value', $request->input('search'));
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('payload->action_label', 'like', "%{$search}%");
            });
        }

        if ($level = $request->input('level')) {
            $query->where('level', $level);
        }

        $recordsTotal = $baseQuery->count();
        $recordsFiltered = $query->count();

        $length = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);

        $query->skip($start)->take($length);

        if ($request->filled('order.0.column')) {
            $direction = $request->input('order.0.dir', 'desc');
            $query->orderBy('created_at', $direction);
        }

        $notifications = $query->get();

        $data = $notifications->map(function (SystemNotification $notification) {
            $presented = $this->present($notification);
            return [
                'title' => $presented['title'],
                'message' => $presented['message'] ?? '—',
                'level' => $this->levelLabel($notification->level),
                'level_badge' => $this->levelBadge($notification->level),
                'user_details' => $this->formatUserDetails($presented['user'] ?? null),
                'date' => $notification->created_at?->format('Y-m-d H:i:s') ?? '—',
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    protected function present(SystemNotification $notification): array
    {
        $user = $notification->payload['user'] ?? $notification->user?->only(['id', 'name', 'email']);

        $action = Arr::get($notification->payload, 'action');
        $actionLabel = $this->translateAction($action);
        $context = Arr::get($notification->payload, 'context', $notification->context);
        $contextLabel = $this->translateContext($context);

        $payload = $notification->payload ?? [];
        $title = $this->resolvePayloadText($payload, 'title') ?? $notification->title;
        if (empty($title) || $title === $action || $title === $context) {
            $title = $actionLabel ?: ($contextLabel ?: $title);
        }

        $message = $this->resolvePayloadText($payload, 'message')
            ?? $notification->message
            ?? Arr::get($payload, 'notification_message')
            ?? $contextLabel;

        $payload['action_label'] = $payload['action_label'] ?? $actionLabel;
        $payload['context_label'] = $payload['context_label'] ?? $contextLabel;

        return [
            'id' => $notification->id,
            'uuid' => $notification->uuid,
            'title' => $title,
            'message' => $message,
            'level' => $notification->level,
            'type' => $notification->type,
            'category' => $notification->category,
            'payload' => $payload,
            'created_at' => $notification->created_at?->diffForHumans(),
            'timestamp' => $notification->created_at?->toDateTimeString(),
            'user' => $user,
            'user_label' => $user['name'] ?? $user['email'] ?? null,
        ];
    }

    protected function categoryCounts(): array
    {
        $base = [
            'notifications' => ['total' => 0, 'unread' => 0],
            'logs' => ['total' => 0, 'unread' => 0],
        ];

        $stats = SystemNotification::selectRaw('type, COUNT(*) as total, SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread')
            ->groupBy('type')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->type => [
                    'total' => (int) $row->total,
                    'unread' => (int) $row->unread,
                ],
            ]);

        foreach ($stats as $type => $counts) {
            $category = SystemNotification::categoryForType($type) === 'logs' ? 'logs' : 'notifications';
            $base[$category]['total'] += $counts['total'];
            $base[$category]['unread'] += $counts['unread'];
        }

        return $base;
    }

    protected function translateAction(?string $action): ?string
    {
        if (! $action) {
            return null;
        }

        $key = 'dashboard.actions_map.' . $action;

        return Lang::has($key) ? __($key) : $action;
    }

    protected function translateContext(?string $context): ?string
    {
        if (! $context) {
            return null;
        }

        $key = 'dashboard.contexts.' . $context;

        return Lang::has($key) ? __($key) : $context;
    }

    protected function levelBadge(string $level): string
    {
        $map = [
            'warning' => 'badge-light-warning text-warning',
            'danger' => 'badge-light-danger text-danger',
            'error' => 'badge-light-danger text-danger',
            'success' => 'badge-light-success text-success',
            'info' => 'badge-light-info text-info',
        ];

        $class = $map[$level] ?? 'badge-light-primary text-primary';
        $label = __('dashboard.notification_levels.' . $level, [], app()->getLocale());

        return sprintf('<span class="badge %s">%s</span>', $class, e($label));
    }

    protected function resolvePayloadText(array $payload, string $type): ?string
    {
        if ($value = $this->localizedFromPayload($payload, $type)) {
            return $value;
        }

        $key = Arr::get($payload, "{$type}_key");

        if ($key && Lang::has($key)) {
            $params = Arr::get($payload, "{$type}_params", []);

            return __($key, is_array($params) ? $params : []);
        }

        return $this->translatePayloadText($payload, $type);
    }

    protected function localizedFromPayload(array $payload, string $type): ?string
    {
        $translations = Arr::get($payload, "{$type}_translations");

        if (is_array($translations) && ! empty($translations)) {
            $locale = app()->getLocale();

            if (isset($translations[$locale])) {
                return $translations[$locale];
            }

            $fallback = config('app.fallback_locale');
            if ($fallback && isset($translations[$fallback])) {
                return $translations[$fallback];
            }

            return reset($translations);
        }

        return null;
    }

    protected function translatePayloadText(array $payload, string $type): ?string
    {
        $key = Arr::get($payload, "{$type}_key");

        if (! $key) {
            return null;
        }

        $params = Arr::get($payload, "{$type}_params", []);

        if (! Lang::has($key)) {
            return null;
        }

        return __($key, is_array($params) ? $params : []);
    }

    protected function levelLabel(string $level): string
    {
        $key = 'dashboard.notification_levels.' . $level;

        return Lang::has($key) ? __($key) : ucfirst($level);
    }

    protected function formatUserDetails(?array $user): string
    {
        if (! $user) {
            return '—';
        }

        $name = Arr::get($user, 'name');
        $email = Arr::get($user, 'email');

        if ($name && $email) {
            return sprintf('<div class="fw-semibold">%s</div><div class="text-muted fs-7">%s</div>', e($name), e($email));
        }

        return e($name ?? $email ?? '—');
    }

    public function importantIndex()
    {
        return view('activity::dashboard.important');
    }
}
