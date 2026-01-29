<?php

namespace Modules\Activity\Http\Controllers\Dashboard;

use Modules\User\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Modules\Activity\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $actions = AuditLog::query()->select('action')->distinct()->pluck('action')->sort()->values();
        $contexts = AuditLog::query()->select('context')->distinct()->pluck('context')->filter()->sort()->values();

        return view('activity::dashboard.index', compact('actions', 'contexts'));
    }

    public function data(Request $request)
    {
        $baseQuery = AuditLog::query()->with('user');
        $query = clone $baseQuery;

        $searchValue = $request->input('search.value', $request->input('search'));

        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('description', 'like', "%{$searchValue}%")
                    ->orWhere('ip_address', 'like', "%{$searchValue}%")
                    ->orWhere('device', 'like', "%{$searchValue}%")
                    ->orWhereHas('user', function ($sub) use ($searchValue) {
                        $sub->where('name', 'like', "%{$searchValue}%")
                            ->orWhere('email', 'like', "%{$searchValue}%");
                    });
            });
        }

        if ($request->filled('actions')) {
            $actions = Arr::wrap($request->input('actions'));
            $query->whereIn('action', $actions);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('contexts')) {
            $contexts = Arr::wrap($request->input('contexts'));
            $query->whereIn('context', $contexts);
        }

        if ($request->filled('date_from')) {
            $query->where('occurred_at', '>=', Carbon::parse($request->input('date_from'))->startOfDay());
        }

        if ($request->filled('date_to')) {
            $query->where('occurred_at', '<=', Carbon::parse($request->input('date_to'))->endOfDay());
        }

        $recordsTotal = $baseQuery->count();
        $recordsFiltered = $query->count();

        $length = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);

        $columns = [
            0 => 'user',
            1 => 'action',
            2 => 'context',
            3 => 'ip_address',
            4 => 'device',
            5 => 'occurred_at',
        ];

        if ($request->filled('order.0.column')) {
            $index = (int) $request->input('order.0.column');
            $direction = $request->input('order.0.dir', 'desc');
            $column = $columns[$index] ?? 'occurred_at';

            if (in_array($column, ['action', 'context', 'ip_address', 'device', 'occurred_at'])) {
                $query->orderBy($column, $direction);
            } else {
                $query->orderBy('occurred_at', 'desc');
            }
        } else {
            $query->orderBy('occurred_at', 'desc');
        }

        $logs = $query->skip($start)->take($length)->get();

        $data = $logs->map(function (AuditLog $log) {
            $actionKey = 'dashboard.actions_map.' . $log->action;
            $contextKey = 'dashboard.contexts.' . ($log->context ?? 'authentication');

            $actionLabel = Lang::hasForLocale($actionKey) ? __($actionKey) : $log->action;
            $contextLabel = Lang::hasForLocale($contextKey) ? __($contextKey) : ($log->context ?? '—');

            $userHtml = view('activity::dashboard.partials.user', compact('log'))->render();

            return [
                'user' => $userHtml,
                'action' => $actionLabel,
                'context' => $contextLabel,
                'ip_address' => $log->ip_address ?? '—',
                'device' => $log->device ?? '—',
                'date' => optional($log->occurred_at)->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function users(Request $request)
    {
        $perPage = 10;
        $page = max(1, (int) $request->input('page', 1));

        $query = User::query()->select('id', 'name', 'email');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $paginator = $query->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        $results = $paginator->map(function (User $user) {
            return [
                'id' => $user->id,
                'text' => "{$user->name} — {$user->email}",
                'name' => $user->name,
                'email' => $user->email,
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $paginator->hasMorePages(),
            ],
        ]);
    }
}
