<?php

use App\Actions\CallSources;
use App\Services\Keepers\Keeper;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::call(function(){
    $keeper = new Keeper();
    $sources = $keeper->getSources();
    CallSources::dispatch($sources);
})->twiceDaily(6, 18);

// Schedule::call(function(){
//     $keeper = new ExampleKeeper();
//     $sources = $keeper->getSources();
//     CallSources::dispatch($sources);
// })->twiceDaily(7, 19);