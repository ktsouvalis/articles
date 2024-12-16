<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Services\Keepers\ValidSourceKeeper;
use App\Interfaces\SourceKeeper;

class CallSourcesConsoleTest extends TestCase
{
    public function testHandleWithoutSourceKeeperArgument()
    {
        $this->artisan('app:call-sources')
            ->expectsOutput('SourceKeeper argument is required.')
            ->assertExitCode(1);
    }

    public function testHandleWithNonExistentClass()
    {
        $this->artisan('app:call-sources NonExistentKeeper')
            ->expectsOutput('Class does not exist in app/Services/Keepers.')
            ->assertExitCode(1);
    }

    public function testHandleWithInvalidSourceKeeper()
    {
        eval('namespace App\Services\Keepers; class InvalidSourceKeeper {}');
        $this->artisan('app:call-sources InvalidSourceKeeper')
            ->expectsOutput('Keeper class must implement SourceKeeper interface.')
            ->assertExitCode(1);
    }

    public function testHandleWithValidSourceKeeper()
    {
        eval('namespace App\Services\Keepers; use App\Interfaces\SourceKeeper; class ValidSourceKeeper implements SourceKeeper{ public function __construct(){} public function getSources(){ return []; } }');
        $this->artisan('app:call-sources ValidSourceKeeper')
            ->expectsOutput('Job dispatched successfully.')
            ->assertExitCode(0);
    }
}
