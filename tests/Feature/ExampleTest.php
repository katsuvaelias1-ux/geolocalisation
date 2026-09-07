<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_guests_are_redirected_to_login_before_entering_the_site(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }
}
