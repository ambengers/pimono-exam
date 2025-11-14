<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sender = User::where('email', 'sender@example.com')->first();
        $receiver = User::where('email', 'receiver@example.com')->first();

        for ($i = 0; $i < 10000; $i++) {
            Transaction::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'amount' => $amount = fake()->randomFloat(2, 1, 100),
                'commission_fee' => $commissionFee = $amount * Transaction::COMMISSION_FEE,
                'commission_fee_percentage' => Transaction::COMMISSION_FEE,
                'total' => $amount + $commissionFee,
                'sender_balance_before' => $sender->balance,
                'sender_balance_after' => $sender->balance - $amount - $amount * Transaction::COMMISSION_FEE,
                'receiver_balance_before' => $receiver->balance,
                'receiver_balance_after' => $receiver->balance + $amount,
            ]);
        }
    }
}
