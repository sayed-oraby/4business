<?php

namespace Modules\User\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rules\Password;
use Modules\Post\Models\Post;
use Modules\Activity\Services\AuditLogger;

class AccountController extends Controller
{
    public function __construct(
        protected AuditLogger $audit
    ) {
    }
    public function dashboard()
    {
        $user = auth()->user();

        $posts = Post::with(['attachments', 'category', 'postType'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $stats = [
            'total_posts' => Post::where('user_id', $user->id)->count(),
            'active_posts' => Post::where('user_id', $user->id)->whereIn('status', ['approved', 'active'])->count(),
            'pending_posts' => Post::where('user_id', $user->id)->where('status', 'pending')->count(),
            // 'total_views' => Post::where('user_id', $user->id)->sum('views_count'),
            'total_views' => 0,

        ];

        return view('user::frontend.account.dashboard', compact('user', 'posts', 'stats'));
    }

    public function edit()
    {
        $user = auth()->user();

        return view('user::frontend.account.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'avatar' => 'nullable|image|max:2048',
        ];

        if ($user->account_type === 'office') {
            $rules['company_name'] = 'required|string|max:255';
            $rules['address'] = 'nullable|string|max:500';
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($user->account_type === 'office') {
            $user->company_name = $validated['company_name'];
            $user->address = $validated['address'];
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الملف الشخصي بنجاح',
                'redirect_url' => route('frontend.account.dashboard'),
            ]);
        }

        return redirect()->route('frontend.account.dashboard')
            ->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    public function password()
    {
        return view('user::frontend.account.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        auth()->user()->update([
            'password' => $request->password,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث كلمة المرور بنجاح',
                'redirect_url' => route('frontend.account.dashboard'),
            ]);
        }

        return redirect()->route('frontend.account.dashboard')
            ->with('success', __('frontend.account.password_updated'));
    }

    public function becomeAgent()
    {
        $user = auth()->user();

        if ($user->account_type === 'office') {
            return redirect()->route('frontend.account.dashboard');
        }

        return view('user::frontend.account.become-agent', compact('user'));
    }

    public function storeBecomeAgent(Request $request)
    {
        $user = auth()->user();

        if ($user->account_type === 'office') {
            return redirect()->route('frontend.account.dashboard');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            // 'license_number' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
        ]);

        $updateData = [
            'office_request_status' => 'pending',
            'company_name' => $validated['company_name'],
            // 'license_number' => $validated['license_number'] ?? null,
            'address' => $validated['address'] ?? null,
        ];

        // Clear rejection reason if resubmitting
        if ($user->office_request_status === 'rejected') {
            $updateData['office_rejection_reason'] = null;
        }

        $user->update($updateData);

        $titleEn = "New request to become an office from {$user->name}";
        $titleAr = "طلب جديد لتصبح مكتبًا من {$user->name}";

        $this->audit->log(
            $user->id,
            'users.become_agent_request',
            $titleEn,
            [
                'context' => 'users',
                'level' => 'success',
                'notification_type' => 'alert',
                'notification_message' => $titleEn,
                'title_translations' => [
                    'en' => $titleEn,
                    'ar' => $titleAr,
                ],
                'message_translations' => [
                    'en' => $titleEn,
                    'ar' => $titleAr,
                ],
                'user_id' => $user->id,
                'company_name' => $validated['company_name'],
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تقديم الطلب بنجاح',
                'redirect_url' => route('frontend.account.dashboard'),
            ]);
        }

        return redirect()->route('frontend.account.dashboard')
            ->with('success', __('frontend.account.become_agent_success'));
    }
}
