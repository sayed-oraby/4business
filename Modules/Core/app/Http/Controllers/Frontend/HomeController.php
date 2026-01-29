<?php

namespace Modules\Core\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Banner\Models\Banner;
use Modules\Category\Models\Category;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostType;
use Modules\Shipping\Models\ShippingState;

class HomeController extends Controller
{
    protected $perPage = 20;

    public function index()
    {
        $categories = Category::where('status', 'active')
            ->whereNull('parent_id')
            ->orderBy('position')
            ->get();

        $postTypes = PostType::where('status', true)
            ->orderBy('id')
            ->get();

        $locations = ShippingState::withCount('cities')
            ->with(['cities' => function ($query) {
                $query->orderBy('name_ar');
            }])
            ->orderBy('name_ar')
            ->get();

        $latestPosts = Post::with(['attachments', 'category', 'postType', 'state', 'city', 'package'])
            ->active()
            ->orderByPriority()
            ->limit($this->perPage)
            ->get();

        $totalPosts = Post::active()->count();

        // Fetch active banners for home_hero placement
        $banners = Banner::placement('home_hero')
            ->activeNow()
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('core::frontend.home', compact('categories', 'postTypes', 'latestPosts', 'banners', 'locations', 'totalPosts'));
    }

    /**
     * Load more posts via AJAX
     */
    public function loadMore(Request $request)
    {
        // Ensure locale is set from header or session
        $locale = $request->header('Accept-Language', session('locale', config('app.locale')));
        app()->setLocale($locale);

        $page = $request->input('page', 1);
        $offset = ($page - 1) * $this->perPage;

        $posts = Post::with(['attachments', 'category', 'postType', 'state', 'city', 'package'])
            ->active()
            ->orderByPriority()
            ->skip($offset)
            ->take($this->perPage)
            ->get();

        $hasMore = Post::active()->count() > ($offset + $posts->count());

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
}
