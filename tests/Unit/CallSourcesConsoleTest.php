<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Actions\CallSources;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Console\Commands\CallSources as JobCallSources;

class CallSourcesConsoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_call_sources_command()
    {
        Queue::fake();

        $this->artisan('app:call-sources')
             ->assertExitCode(0);
    }

    public function test_call_sources_command_failure()
    {
        Queue::fake();

        Queue::shouldReceive('push')
            ->andThrow(new \Exception('Exception message'));

        
        $this->artisan('app:call-sources')
            ->assertExitCode(1);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}