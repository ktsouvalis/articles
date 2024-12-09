<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LastCallService
{
    public function getLastCall($sourceName){
        $lastCall = DB::table('last_news')->where('name', $sourceName)->value('last_call');
        return $lastCall ? Carbon::parse($lastCall)->toDateString() : Carbon::now()->subDay()->toDateString();
    }

    public function updateLastCall($sourceName){
        DB::table('last_news')->updateOrInsert(
            ['name' => $sourceName],
            ['last_call' => Carbon::now()]
        );
    }
}