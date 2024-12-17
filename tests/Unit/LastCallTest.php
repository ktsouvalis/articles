<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\LastCall;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LastCallTest extends TestCase
{
    use RefreshDatabase;

    public function testGetLastCallReturnsCorrectDate()
    {
        $sourceName = 'test_source';
        $lastCallDate = Carbon::now()->subDays(2)->toDateString();

        DB::table('last_news')->insert([
            'name' => $sourceName,
            'last_call' => $lastCallDate,
        ]);

        $lastCallService = new LastCall();
        $result = $lastCallService->getLastCall($sourceName);

        $this->assertEquals($lastCallDate, $result);
    }

    public function testGetLastCallReturnsYesterdayIfNoRecord()
    {
        $sourceName = 'test_source';
        $yesterday = Carbon::now()->subDay()->toDateString();

        $lastCallService = new LastCall();
        $result = $lastCallService->getLastCall($sourceName);

        $this->assertEquals($yesterday, $result);
    }

    public function testUpdateLastCallInsertsOrUpdateRecord()
    {
        $sourceName = 'test_source';
        $lastCallService = new LastCall();

        $lastCallService->updateLastCall($sourceName);

        $this->assertDatabaseHas('last_news', [
            'name' => $sourceName,
        ]);

        $lastCallService->updateLastCall($sourceName);

        $this->assertDatabaseCount('last_news', 1);
    }
}
