<?php

namespace App\Console\Commands;

use App\Services\GHLService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncDecoNtwrkToGHL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales-orders:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs DecoNTwrk to GHL';

    protected $ghlService;




    /**
     * Create a new command instance.
     */
    public function __construct(GHLService $ghlService)
    {
        parent::__construct();
        $this->ghlService = $ghlService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sales orders synchronization...');

        $this->ghlService->syncOrders();

        $this->info('Sales orders synchronization completed!');
    }
}

