<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ExampleTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_guest_can_open_landing_page(): void
    {
        $route = Route::getRoutes()->match(Request::create('/'));

        $this->assertNotContains('auth', $route->gatherMiddleware());
    }
}
