# Activity Module

Provides a queue-ready audit logging pipeline that records every sensitive action (logins, password resets, etc.) and exposes a Metronic-powered dashboard page (`/dashboard/audit-logs`).

## Components
- `AuditLog` model with rich metadata (IP, agent, device, JSON properties).
- `AuditLogger` service (singleton) that can be injected anywhere.
- `RecordAuditLogJob` job so logs are persisted asynchronously without blocking UI/API requests.
- Dashboard UI (AJAX filters + search) for administrators.

## Logging a new action
```php
use Modules\Activity\App\Jobs\RecordAuditLogJob;

RecordAuditLogJob::dispatch(
    auth()->id(),                 // actor id
    'orders.refund',              // action key
    'Order refunded by admin',    // human readable description
    ['context' => 'orders', 'order_id' => $order->id],
    'admin',                      // guard or channel
    request()->ip(),
    request()->userAgent()
);
```
All properties are optional but recommended so that future API/front-end clients can re-use the same job. Every job writes to `audit_logs` and can be extended with broadcast events or external sinks.

## Queue & Real-time
The job implements `ShouldQueue`, so Horizon/Redis handle throughput. Because actions are normalized (`auth.login`, `orders.create`, ...), they can later be streamed via Reverb/WebSockets without changing existing code.

## System notifications & toasts
- `SystemNotification` model + migration track actionable alerts (alerts/updates/logs) with a friendly accessor `category` (alerts, updates, logs).
- `NotificationPublisher` service (singleton) persists a notification and broadcasts `SystemNotificationCreated` on the `private-dashboard.notifications` channel.
- Dashboard header subscribes via Echo and renders Metronic toasts + menu badges while audit events stay out of the badge count (they still show in the “Logs” tab).

### Publishing a notification
```php
use Modules\Activity\Services\NotificationPublisher;

public function handle(Order $order, NotificationPublisher $publisher)
{
    $publisher->publish([
        'type' => 'update',                // alert|update|audit (defaults to alert)
        'level' => 'success',              // info|success|warning|danger
        'title' => __('orders::messages.shipped_title'),
        'message' => __('orders::messages.shipped_body', ['order' => $order->number]),
        'payload' => [
            'action' => 'orders.shipped',
            'order_id' => $order->id,
        ],
        'user_id' => auth()->id(),
    ]);
}
```

### Logging vs notifications
- Use `RecordAuditLogJob`/`AuditLogger` for compliance trails (context + IP/device). These events create `type=audit` notifications (toast only, no badge count).
- Use `NotificationPublisher` for user-facing alerts (new order, payout failure, etc.). These increment the bell badge and populate the alerts/updates tabs.

### Extending on the front-end
The dashboard exposes `window.GavanKit.notifications` with `feedUrl`, `markAllUrl`, and translated copy. Any module can push toasts by broadcasting the `SystemNotificationCreated` event (or simply using the publisher above). The dropdown is built with data attributes, so modules can append more sections or tabs if needed.
