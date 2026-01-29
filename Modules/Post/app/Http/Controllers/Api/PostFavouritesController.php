<?php

namespace Modules\Post\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\User\Http\Requests\WebService\StoreFavouriteRequest;
use Modules\User\Entities\UserFavourite;
use App\Support\ApiResponse;
use Illuminate\Routing\Controller;
use Modules\Post\Http\Requests\Api\StoreFavouriteRequest as ApiStoreFavouriteRequest;
use Modules\Post\Http\Resources\PostSummaryResource;
use Modules\Post\Models\Fav;
use Modules\Post\Models\Post;
use Modules\Post\Services\PostService;
use Modules\User\Models\User;

class PostFavouritesController extends Controller
{
    use ApiResponse;

    public function __construct(protected PostService $postService) { }


    public function list(Request $request)
    {
        $user = $request->user();

        $itemIds = $user->favourites()->pluck('post_id');

        $favouritesPosts = Post::whereIn('uuid',$itemIds)
        ->with(['attachments', 'category', 'postType', 'city', 'state', 'package'])->get();

        return $this->successResponse(
            data: PostSummaryResource::collection($favouritesPosts)->resolve(),
            message: __('post::post.messages.retrieved')
        );

    }


    public function store(ApiStoreFavouriteRequest $request)
    {
        $user = $request->user();

        $favourite = Fav::where('user_id',$user->id)->where('post_id',$request->post_id)->first();

        if($favourite != null) {

            $favourite->delete();

            $is_fav = false;

            $msg = __('post::post.messages.deleted');

        } else {

            $favourite = Fav::create([
                'user_id' => $user->id,
                'post_id' => $request->post_id,
            ]);

            $is_fav = true;

            $msg =  __('post::post.messages.added');
        }

         $data = [
            'is_fav'          => $is_fav,
            "favouritesCount" => $user->favourites()->count(),
        ];

        return $this->successResponse($data,$msg);

    }



}
