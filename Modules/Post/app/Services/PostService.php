<?php

namespace Modules\Post\Services;

use Modules\Activity\Services\AuditLogger;
use Modules\Post\Models\Post;
use Modules\User\Models\User;

class PostService
{
    public function __construct(
        protected AuditLogger $audit
    ) {
    }

    /**
     * Notify admins about a new post creation.
     */
    public function notifyNewPost(Post $post, User $user): void
    {
        // Pre-build translations for both languages
        $params = [
            'user_name' => $user->name,
            'post_title' => $post->title,
        ];
        
        $titleEn = "New post \"{$post->title}\" created by {$user->name}";
        $titleAr = "منشور جديد \"{$post->title}\" تم إنشاؤه بواسطة {$user->name}";

        $this->audit->log(
            $user->id,
            'posts.created',
            $titleEn, // Default to English
            [
                'context' => 'posts',
                'level' => 'success',
                'notification_type' => 'alert',
                'notification_message' => $titleEn,
                // Pre-built translations for dashboard
                'title_translations' => [
                    'en' => $titleEn,
                    'ar' => $titleAr,
                ],
                'message_translations' => [
                    'en' => $titleEn,
                    'ar' => $titleAr,
                ],
                'post_id' => $post->id,
                'post_uuid' => $post->uuid,
            ]
        );
    }

    /**
     * Notify user about post approval.
     */
    public function notifyPostApproved(Post $post): void
    {
        $titleEn = "Your post \"{$post->title}\" has been approved.";
        $titleAr = "تمت الموافقة على منشورك \"{$post->title}\".";

        $this->audit->log(
            $post->user_id,
            'posts.approved',
            $titleEn,
            [
                'context' => 'posts',
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
                'post_id' => $post->id,
            ]
        );
    }

    /**
     * Notify user about post rejection.
     */
    public function notifyPostRejected(Post $post, ?string $reason = null): void
    {
        $reasonText = $reason ?? 'No reason provided';
        $reasonTextAr = $reason ?? 'لم يتم تحديد سبب';
        
        $titleEn = "Your post \"{$post->title}\" has been rejected. Reason: {$reasonText}";
        $titleAr = "تم رفض منشورك \"{$post->title}\". السبب: {$reasonTextAr}";

        $this->audit->log(
            $post->user_id,
            'posts.rejected',
            $titleEn,
            [
                'context' => 'posts',
                'level' => 'warning',
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
                'post_id' => $post->id,
                'rejection_reason' => $reason,
            ]
        );
    }

    /**
     * Notify user about successful payment for their post.
     */
    public function notifyPaymentSuccess(Post $post): void
    {
        $titleEn = "Payment for your post \"{$post->title}\" was successful.";
        $titleAr = "تم الدفع بنجاح لمنشورك \"{$post->title}\".";

        $this->audit->log(
            $post->user_id,
            'posts.payment_success',
            $titleEn,
            [
                'context' => 'posts',
                'level' => 'success',
                'notification_type' => 'important',
                'notification_message' => $titleEn,
                'title_translations' => [
                    'en' => $titleEn,
                    'ar' => $titleAr,
                ],
                'message_translations' => [
                    'en' => $titleEn,
                    'ar' => $titleAr,
                ],
                'post_id' => $post->id,
            ]
        );
    }
}

