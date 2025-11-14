<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionsShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_transaction_show_endpoint(): void
    {
        $transaction = Transaction::factory()->create();

        $response = $this->getJson("/api/transactions/{$transaction->id}");

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_view_their_sent_transaction(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        $transaction = Transaction::factory()->create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/transactions/{$transaction->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'sender_id',
                'receiver_id',
                'amount',
                'commission_fee',
                'commission_fee_percentage',
                'total',
                'sender_balance_before',
                'sender_balance_after',
                'created_at',
            ]
        ]);
        
        $response->assertJson([
            'data' => [
                'id' => $transaction->id,
                'sender_id' => $user->id,
                'receiver_id' => $receiver->id,
            ]
        ]);
    }

    public function test_authenticated_user_can_view_their_received_transaction(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();

        $transaction = Transaction::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/transactions/{$transaction->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $transaction->id,
                'sender_id' => $sender->id,
                'receiver_id' => $user->id,
            ]
        ]);
    }

    public function test_user_cannot_view_transaction_they_are_not_involved_in(): void
    {
        $user = User::factory()->create();
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        $transaction = Transaction::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/transactions/{$transaction->id}");

        $response->assertStatus(404);
    }

    public function test_transaction_show_includes_sender_and_receiver_data(): void
    {
        $user = User::factory()->create(['name' => 'Sender User']);
        $receiver = User::factory()->create(['name' => 'Receiver User']);

        $transaction = Transaction::factory()->create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/transactions/{$transaction->id}");

        $response->assertStatus(200);
        $transactionData = $response->json('data');
        
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

        $response = $this->actingAs($user)->getJson("/api/transactions/{$transaction->id}");

        $response->assertStatus(200);
        $transactionData = $response->json('data');
        
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

        $response = $this->actingAs($user)->getJson("/api/transactions/{$transaction->id}");

        $response->assertStatus(200);
        $transactionData = $response->json('data');
        
        $this->assertArrayHasKey('receiver_balance_before', $transactionData);
        $this->assertArrayHasKey('receiver_balance_after', $transactionData);
        $this->assertEquals(500.00, $transactionData['receiver_balance_before']);
        $this->assertEquals(600.00, $transactionData['receiver_balance_after']);
    }

    public function test_returns_404_for_nonexistent_transaction(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/transactions/99999');

        $response->assertStatus(404);
    }

    public function test_returns_404_for_invalid_transaction_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/transactions/invalid-id');

        $response->assertStatus(404);
    }

    public function test_transaction_show_response_contains_all_required_fields(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        $transaction = Transaction::factory()->create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/transactions/{$transaction->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'sender_id',
                'receiver_id',
                'amount',
                'commission_fee',
                'commission_fee_percentage',
                'total',
                'created_at',
            ]
        ]);
    }

    public function test_transaction_show_does_not_include_sensitive_data(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        $transaction = Transaction::factory()->create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/transactions/{$transaction->id}");

        $response->assertStatus(200);
        $transactionData = $response->json('data');
        
        // Verify sender and receiver don't include sensitive data
        if (isset($transactionData['sender'])) {
            $this->assertArrayNotHasKey('password', $transactionData['sender']);
            $this->assertArrayNotHasKey('remember_token', $transactionData['sender']);
        }
        
        if (isset($transactionData['receiver'])) {
            $this->assertArrayNotHasKey('password', $transactionData['receiver']);
            $this->assertArrayNotHasKey('remember_token', $transactionData['receiver']);
        }
    }
}

