<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ThrottleTest extends TestCase
{
    /**
     * Test the throttle middleware.
     *
     * @return void
     */
    public function testThrottleMiddleware()
    {
        for ($i = 0; $i < 101; $i++) {
            $response = $this->get('/api/articles');
        }

        $response->assertStatus(429);
    }
}
