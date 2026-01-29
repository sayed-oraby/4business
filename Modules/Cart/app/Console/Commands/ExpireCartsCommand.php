<?php

namespace Modules\Cart\Console\Commands;

use Illuminate\Console\Command;
use Modules\Cart\Repositories\CartRepository;

class ExpireCartsCommand extends Command
{
    protected $signature = 'cart:expire';

    protected $description = 'Expire carts that have passed their lifetime.';

    public function __construct(
        protected CartRepository $repository
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $count = $this->repository->expireOldCarts();
        $this->info("Expired {$count} carts.");

        return self::SUCCESS;
    }
}
