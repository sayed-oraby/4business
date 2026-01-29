<?php

namespace Modules\Setting\Http\Controllers\Dashboard;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Setting\Models\ContactMessage;

class ContactMessageController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware(['auth:admin', 'permission:dashboard.access']);
    }

    /**
     * Display the contact messages listing page
     */
    public function index()
    {
        $stats = [
            'total' => ContactMessage::count(),
            'pending' => ContactMessage::where('status', 'pending')->count(),
            'read' => ContactMessage::where('status', 'read')->count(),
            'replied' => ContactMessage::where('status', 'replied')->count(),
            'closed' => ContactMessage::where('status', 'closed')->count(),
        ];

        return view('setting::dashboard.contact-messages.index', compact('stats'));
    }

    /**
     * Get contact messages data for DataTable
     */
    public function data(Request $request): JsonResponse
    {
        $query = ContactMessage::with('user')
            ->orderByRaw("FIELD(status, 'pending', 'read', 'replied', 'closed')")
            ->latest();

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Search
        if ($searchValue = $request->input('search.value')) {
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhere('phone', 'like', "%{$searchValue}%")
                  ->orWhere('subject', 'like', "%{$searchValue}%")
                  ->orWhere('message', 'like', "%{$searchValue}%");
            });
        }

        $recordsTotal = ContactMessage::count();
        $recordsFiltered = $query->count();

        $messages = $query->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $messages,
        ]);
    }

    /**
     * Show a specific contact message
     */
    public function show(ContactMessage $contactMessage): JsonResponse
    {
        // Mark as read if pending
        if ($contactMessage->status === 'pending') {
            $contactMessage->update(['status' => 'read']);
        }

        return response()->json([
            'message' => $contactMessage->load('user'),
        ]);
    }

    /**
     * Update contact message status
     */
    public function updateStatus(Request $request, ContactMessage $contactMessage): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,read,replied,closed',
        ]);

        $contactMessage->update(['status' => $validated['status']]);

        return response()->json([
            'message' => __('setting::contact_messages.messages.status_updated'),
        ]);
    }

    /**
     * Delete a contact message
     */
    public function destroy(ContactMessage $contactMessage): JsonResponse
    {
        $contactMessage->delete();

        return response()->json([
            'message' => __('setting::contact_messages.messages.deleted'),
        ]);
    }

    /**
     * Bulk delete contact messages
     */
    public function bulkDestroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['message' => 'لم يتم تحديد أي رسائل.'], 422);
        }

        $count = ContactMessage::whereIn('id', $ids)->delete();

        return response()->json([
            'message' => __('setting::contact_messages.messages.bulk_deleted', ['count' => $count]),
            'count' => $count,
        ]);
    }
}
