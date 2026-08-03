<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response
            ->assertStatus(200)
            ->assertViewIs('home')
            ->assertSee('Run every campus from one')
            ->assertSee(route('api.health'), false)
            ->assertSee(route('modules.health'), false);
    }
}
