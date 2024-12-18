<?php

namespace App\Cron;

use Illuminate\Support\Facades\Artisan;

Artisan::call('opportunity-stages:sync');
