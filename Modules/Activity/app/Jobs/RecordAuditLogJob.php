<?php

namespace Modules\Activity\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Activity\Services\AuditLogger;

class RecordAuditLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected ?int $userId,
        protected string $action,
        protected ?string $description = null,
        protected array $properties = [],
        protected ?string $guard = null,
        protected ?string $ip = null,
        protected ?string $userAgent = null
    ) {
        $this->queue = 'default';
    }

    /**
     * Execute the job.
     */
    public function handle(AuditLogger $logger): void
    {
        $properties = array_merge($this->properties, [
            '_queued' => true,
        ]);

        $logger->log($this->userId, $this->action, $this->description, $properties, $this->guard, $this->ip, $this->userAgent);
    }
}
