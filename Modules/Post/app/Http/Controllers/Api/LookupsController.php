<?php

namespace Modules\Post\Http\Controllers\Api;

use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Post\Models\Package;
use Modules\Post\Models\PostType;
use Modules\Post\Models\Skill;

class LookupsController extends Controller
{
    use ApiResponse;

    public function postTypes(): JsonResponse
    {
        $locale = app()->getLocale();
        $types = PostType::where('status', true)->get();

        $result = $types->map(function ($type) use ($locale) {
            return [
                'id' => $type->id,
                'name' => $type->getTranslation('name', $locale),
            ];
        });

        return $this->successResponse(
            data: ['post_types' => $result],
            message: __('post::post.messages.post_types_retrieved')
        );
    }

    public function packages(): JsonResponse
    {
        $locale = app()->getLocale();
        $packages = Package::where('status', true)->get();

        $user = auth('api')->user();
        $result = $packages->map(function ($package) use ($locale, $user) {
            $isDisabled = false;
            $disabledReason = null;

            if ($package->is_free && $user) {
                $usedFreeCredits = \Modules\Post\Models\Post::where('user_id', $user->id)
                    ->where('package_id', $package->id)
                    ->count();
                $freeCreditsTotal = $package->free_credits_per_user ?? 0;
                $remaining = max(0, $freeCreditsTotal - $usedFreeCredits);

                if ($remaining <= 0) {
                    $isDisabled = true;
                    $disabledReason = __('post::post.messages.free_limit_reached');
                }
            }

            return [
                'id' => $package->id,
                'title' => $package->getTranslation('title', $locale),
                'description' => $package->getTranslation('description', $locale),
                'price' => $package->price,
                'period_days' => $package->period_days,
                'top_days' => $package->top_days,
                'cover_image_url' => $package->cover_image_url,
                'is_featured' => $package->is_featured,
                'label_color' => $package->label_color,
                'card_color' => $package->card_color,
                'is_free' => (bool) $package->is_free,
                'is_disabled' => $isDisabled,
                'disabled_reason' => $disabledReason,
            ];
        });

        return $this->successResponse(
            data: ['packages' => $result],
            message: __('post::post.messages.packages_retrieved')
        );
    }

    public function skills(): JsonResponse
    {
        $skills = Skill::get(['id', 'name', 'slug']);

        return $this->successResponse(
            data: ['skills' => $skills],
            message: __('post::post.messages.skills_retrieved')
        );
    }
}
