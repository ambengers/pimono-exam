<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_accounts_endpoint(): void
    {
        $response = $this->getJson('/api/accounts');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_access_accounts_endpoint(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/accounts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'email']
            ],
            'meta',
            'links'
        ]);
    }

    public function test_current_user_is_excluded_from_results(): void
    {
        $user = User::factory()->create([
            'name' => 'Current User',
            'email' => 'current@example.com',
        ]);

        $otherUser = User::factory()->create([
            'name' => 'Other User',
            'email' => 'other@example.com',
        ]);

        $response = $this->actingAs($user)->getJson('/api/accounts');

        $response->assertStatus(200);
        $response->assertJsonMissing([
            'id' => $user->id,
        ]);
        $response->assertJsonFragment([
            'id' => $otherUser->id,
            'name' => $otherUser->name,
            'email' => $otherUser->email,
        ]);
    }

    public function test_can_search_accounts_by_id(): void
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create(['id' => 999]);
        User::factory()->create(['id' => 888]);

        $response = $this->actingAs($user)->getJson('/api/accounts?search=999');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => 999,
            'name' => $targetUser->name,
            'email' => $targetUser->email,
        ]);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_can_search_accounts_by_name(): void
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create(['name' => 'John Doe']);
        User::factory()->create(['name' => 'Jane Smith']);

        $response = $this->actingAs($user)->getJson('/api/accounts?search=John');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $targetUser->id,
            'name' => 'John Doe',
        ]);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_can_search_accounts_by_email(): void
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create(['email' => 'john@example.com']);
        User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->actingAs($user)->getJson('/api/accounts?search=john@example.com');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $targetUser->id,
            'email' => 'john@example.com',
        ]);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
    }

    public function test_search_is_case_insensitive(): void
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create(['name' => 'John Doe']);

        $response = $this->actingAs($user)->getJson('/api/accounts?search=JOHN');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $targetUser->id,
            'name' => 'John Doe',
        ]);
    }

    public function test_returns_empty_results_when_no_match(): void
    {
        $user = User::factory()->create();
        User::factory()->create(['name' => 'John Doe']);

        $response = $this->actingAs($user)->getJson('/api/accounts?search=Nonexistent');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(0, $data);
    }

    public function test_returns_paginated_results(): void
    {
        $user = User::factory()->create();
        
        // Create 60 users (more than the pagination limit of 50)
        User::factory()->count(60)->create();

        $response = $this->actingAs($user)->getJson('/api/accounts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
                'from',
                'to',
            ],
            'links'
        ]);
        
        $meta = $response->json('meta');
        $this->assertEquals(50, $meta['per_page']);
        $this->assertEquals(60, $meta['total']);
        $this->assertGreaterThan(1, $meta['last_page']);
    }

    public function test_response_contains_correct_account_structure(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $response = $this->actingAs($user)->getJson('/api/accounts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                ]
            ]
        ]);
        
        $firstAccount = $response->json('data.0');
        $this->assertArrayHasKey('id', $firstAccount);
        $this->assertArrayHasKey('name', $firstAccount);
        $this->assertArrayHasKey('email', $firstAccount);
        $this->assertArrayNotHasKey('password', $firstAccount);
        $this->assertArrayNotHasKey('balance', $firstAccount);
    }
}

