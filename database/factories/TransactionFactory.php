<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 1, 1000);
        $commissionFee = $amount * Transaction::COMMISSION_FEE;
        $total = $amount + $commissionFee;

        return [
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'amount' => $amount,
            'commission_fee' => $commissionFee,
            'commission_fee_percentage' => Transaction::COMMISSION_FEE,
            'total' => $total,
            'sender_balance_before' => fake()->randomFloat(2, 100, 10000),
            'sender_balance_after' => fake()->randomFloat(2, 100, 10000),
            'receiver_balance_before' => fake()->randomFloat(2, 100, 10000),
            'receiver_balance_after' => fake()->randomFloat(2, 100, 10000),
        ];
    }
}

