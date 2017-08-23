<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Subscription;
use Illuminate\Console\Command;

class PurgeSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purges subscriptions that have expired and will not renew.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Subscription::where('cancels_at', '!=', null)
            ->where('cancels_at', '<=', Carbon::now())
            ->delete();
    }
}
