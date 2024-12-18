<?php

namespace App\Console\Commands;

use App\Services\GHLService;
use Illuminate\Console\Command;

class CreateGHLUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-ghl-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
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
        $this->info('Creating GHL users...');
        $this->ghlService->createGHLUsers();
        $this->info('GHL users created!');
    }
}
