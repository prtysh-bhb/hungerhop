<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Laravel 12 supports better test performance
        $this->artisan('config:clear');

        // Seed basic data if needed
        // $this->seed(BasicSeeder::class);
    }

    /**
     * Helper method for Laravel 12 API testing
     */
    protected function actingAsUser($user = null): static
    {
        $user = $user ?? \App\Models\User::factory()->create();

        return $this->actingAs($user);
    }
}
