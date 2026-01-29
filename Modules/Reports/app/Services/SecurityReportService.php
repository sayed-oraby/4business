<?php

namespace Modules\Reports\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Activity\Models\AuditLog;

class SecurityReportService
{
    /**
     * Get login attempts report
     */
    public function getLoginAttempts(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = AuditLog::query()
            ->where('action', 'login')
            ->orWhere('action', 'logout');

        if ($startDate && $endDate) {
            $query->whereBetween('occurred_at', [$startDate, $endDate]);
        }

        $logs = $query->get();

        $successful = $logs->where('action', 'login')->count();
        $failed = $logs->where('action', 'login')->where('properties->success', false)->count();

        // Group by IP and device
        $byIp = $logs->groupBy('ip_address')->map(function ($group) {
            return [
                'ip_address' => $group->first()->ip_address,
                'attempts_count' => $group->count(),
                'last_attempt' => $group->max('occurred_at')->format('Y-m-d H:i:s'),
            ];
        })->values()->toArray();

        $byDevice = $logs->groupBy('device')->map(function ($group) {
            return [
                'device' => $group->first()->device ?? 'Unknown',
                'attempts_count' => $group->count(),
            ];
        })->values()->toArray();

        return [
            'successful_attempts' => $successful,
            'failed_attempts' => $failed,
            'by_ip' => $byIp,
            'by_device' => $byDevice,
        ];
    }

    /**
     * Get ban/block report
     */
    public function getBansReport(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        // This would require a bans/blocks table
        return [
            'message' => 'Bans report requires bans table implementation',
        ];
    }

    /**
     * Get admin audit log report
     */
    public function getAdminAuditLog(?Carbon $startDate = null, ?Carbon $endDate = null, array $filters = []): array
    {
        $query = AuditLog::query()
            ->where('guard', 'admin')
            ->with('user');

        if ($startDate && $endDate) {
            $query->whereBetween('occurred_at', [$startDate, $endDate]);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['context'])) {
            $query->where('context', $filters['context']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query
            ->orderByDesc('occurred_at')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user?->name ?? 'System',
                    'action' => $log->action,
                    'context' => $log->context,
                    'description' => $log->description,
                    'ip_address' => $log->ip_address,
                    'device' => $log->device,
                    'occurred_at' => $log->occurred_at->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();
    }

    /**
     * Get admin actions summary
     */
    public function getAdminActionsSummary(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = AuditLog::query()
            ->where('guard', 'admin');

        if ($startDate && $endDate) {
            $query->whereBetween('occurred_at', [$startDate, $endDate]);
        }

        return $query
            ->select([
                'action',
                'context',
                DB::raw('COUNT(*) as count'),
            ])
            ->groupBy('action', 'context')
            ->get()
            ->map(function ($item) {
                return [
                    'action' => $item->action,
                    'context' => $item->context,
                    'count' => (int) $item->count,
                ];
            })
            ->toArray();
    }
}
