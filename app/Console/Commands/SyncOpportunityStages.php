<?php

namespace App\Console\Commands;

use App\Services\GHLService;
use Illuminate\Console\Command;

class SyncOpportunityStages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'opportunity-stages:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs Ghl Opportunity Stages';
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

        $this->info('Starting stage id update...');

        $this->ghlService->checkAndUpdateStageId();

        $this->info('Stage id update completed!');
    }
}
