<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_transactions_endpoint(): void
    {
        $response = $this->getJson('/api/transactions');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_access_transactions_endpoint(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/transactions');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'sender_id',
                    'receiver_id',
                    'amount',
                    'commission_fee',
                    'commission_fee_percentage',
                    'total',
                    'created_at',
                ]
            ],
            'meta',
            'links'
        ]);
    }

    public function test_user_can_see_transactions_where_they_are_sender(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        $transaction = Transaction::factory()->create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/transactions');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $transaction->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
        ]);
    }

    public function test_user_can_see_transactions_where_they_are_receiver(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $transaction = Transaction::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/transactions');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $transaction->id,
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
        ]);
    }

    public function test_user_cannot_see_transactions_they_are_not_involved_in(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $transaction = Transaction::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/transactions');

        $response->assertStatus(200);
        $response->assertJsonMissing([
            'id' => $transaction->id,
        ]);
    }

    public function test_transactions_include_sender_and_receiver_data(): void
    {
        $user = User::factory()->create(['name' => 'Sender User']);
        $receiver = User::factory()->create(['name' => 'Receiver User']);

        $transaction = Transaction::factory()->create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/transactions');

        $response->assertStatus(200);
        $transactionData = collect($response->json('data'))->firstWhere('id', $transaction->id);
        
        $this->assertNotNull($transactionData);
        $this->assertArrayHasKey('sender', $transactionData);
        $this->assertArrayHasKey('receiver', $transactionData);
        $this->assertEquals('Sender User', $transactionData['sender']['name']);
        $this->assertEquals('Receiver User', $transactionData['receiver']['name']);
    }

    public function test_sender_balance_fields_are_included_when_user_is_sender(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        $transaction = Transaction::factory()->create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'sender_balance_before' => 1000.00,
            'sender_balance_after' => 985.00,
        ]);

        $response = $this->actingAs($user)->getJson('/api/transactions');

        $response->assertStatus(200);
        $transactionData = collect($response->json('data'))->firstWhere('id', $transaction->id);
        
        $this->assertNotNull($transactionData);
        $this->assertArrayHasKey('sender_balance_before', $transactionData);
        $this->assertArrayHasKey('sender_balance_after', $transactionData);
        $this->assertEquals(1000.00, $transactionData['sender_balance_before']);
        $this->assertEquals(985.00, $transactionData['sender_balance_after']);
    }

    public function test_receiver_balance_fields_are_included_when_user_is_receiver(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $transaction = Transaction::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
            'receiver_balance_before' => 500.00,
            'receiver_balance_after' => 600.00,
        ]);

        $response = $this->actingAs($user)->getJson('/api/transactions');

        $response->assertStatus(200);
        $transactionData = collect($response->json('data'))->firstWhere('id', $transaction->id);
        
        $this->assertNotNull($transactionData);
        $this->assertArrayHasKey('receiver_balance_before', $transactionData);
        $this->assertArrayHasKey('receiver_balance_after', $transactionData);
        $this->assertEquals(500.00, $transactionData['receiver_balance_before']);
        $this->assertEquals(600.00, $transactionData['receiver_balance_after']);
    }

    public function test_transactions_are_ordered_by_latest_first(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        $oldTransaction = Transaction::factory()->create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'created_at' => now()->subDays(2),
        ]);

        $newTransaction = Transaction::factory()->create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->getJson('/api/transactions');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertEquals($newTransaction->id, $data[0]['id']);
        $this->assertEquals($oldTransaction->id, $data[1]['id']);
    }

    public function test_returns_paginated_results(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        // Create 20 transactions (more than the pagination limit of 15)
        Transaction::factory()->count(20)->create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/transactions');

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
        $this->assertEquals(15, $meta['per_page']);
        $this->assertEquals(20, $meta['total']);
        $this->assertGreaterThan(1, $meta['last_page']);
    }

    public function test_can_paginate_to_second_page(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        // Create 20 transactions
        Transaction::factory()->count(20)->create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/transactions?page=2');

        $response->assertStatus(200);
        $meta = $response->json('meta');
        $this->assertEquals(2, $meta['current_page']);
        $this->assertEquals(20, $meta['total']);
        
        $data = $response->json('data');
        $this->assertCount(5, $data); // 20 total - 15 on first page = 5 on second page
    }
}

