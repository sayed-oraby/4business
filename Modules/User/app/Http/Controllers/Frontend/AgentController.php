<?php

namespace Modules\User\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Post\Models\Post;
use Modules\User\Models\User;

class AgentController extends Controller
{
    protected $perPage = 20;

    public function index(Request $request)
    {
        // Get users who have active posts (agents/advertisers)
        $agents = User::where('account_type', 'office')
            ->withCount(['posts' => fn ($q) => $q->active()])
            ->has('posts')
            ->orderByDesc('posts_count')
            ->paginate(12);

        return view('user::frontend.agents.index', compact('agents'));
    }

    public function show(string $slug)
    {
        // Find agent by ID (since slug doesn't exist)
        $agent = User::findOrFail($slug);

        $posts = Post::with(['attachments', 'category', 'postType', 'state', 'city', 'package'])
            ->where('user_id', $agent->id)
            ->active()
            ->orderByPriority()
            ->limit($this->perPage)
            ->get();

        $totalPosts = Post::where('user_id', $agent->id)->active()->count();
        $hasMore = $totalPosts > $this->perPage;

        return view('user::frontend.agents.show', compact('agent', 'posts', 'totalPosts', 'hasMore'));
    }

    /**
     * Load more posts for agent via AJAX
     */
    public function loadMore(Request $request, string $agentId)
    {
        $agent = User::findOrFail($agentId);

        $page = $request->input('page', 1);
        $offset = ($page - 1) * $this->perPage;

        // For page 1, we already loaded initial posts, so start from page 2
        $offset = $page * $this->perPage;

        $posts = Post::with(['attachments', 'category', 'postType', 'state', 'city', 'package'])
            ->where('user_id', $agent->id)
            ->active()
            ->orderByPriority()
            ->skip($offset)
            ->take($this->perPage)
            ->get();

        $totalPosts = Post::where('user_id', $agent->id)->active()->count();
        $hasMore = $totalPosts > ($offset + $posts->count());

        $html = '';
        foreach ($posts as $post) {
            $html .= view('post::frontend.partials.post-card-horizontal', compact('post'))->render();
        }

        return response()->json([
            'html' => $html,
            'hasMore' => $hasMore,
            'count' => $posts->count(),
        ]);
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
            'active_posts' => Post::where('user_id', $user->id)->active()->count(),
            'pending_posts' => Post::where('user_id', $user->id)->where('status', 'pending')->count(),
            'total_views' => 0, // views_count doesn't exist in posts table
        ];

        return view('user::frontend.account.dashboard', compact('user', 'posts', 'stats'));
    }
}
