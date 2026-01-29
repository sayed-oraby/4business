<?php

namespace Modules\Post\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Post\Http\Requests\Api\StoreJobOfferRequest;
use Modules\Post\Models\JobOffer;
use Modules\Post\Models\Post;

class JobOfferController extends Controller
{
    use ApiResponse;

    public function store(StoreJobOfferRequest $request, Post $post): JsonResponse
    {
        // Ensure the user is not offering a job to themselves
        if ($request->user()->id === $post->user_id) {
            return $this->errorResponse(__('post::post.messages.cannot_offer_own_post'), 403);
        }

        $jobOffer = $post->jobOffers()->create([
            'user_id' => $request->user()->id,
            'joining_date' => $request->joining_date,
            'salary' => $request->salary,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return $this->successResponse(
            data: ['job_offer' => $jobOffer],
            message: __('post::post.messages.job_offer_sent'),
            status: 201
        );
    }
}
