<?php

use App\Actions\CallSources;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::call(function(){
    CallSources::dispatch();
})->twiceDaily(6, 18);