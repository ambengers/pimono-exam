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
                'commission_fee' => $amount * Transaction::COMMISSION_FEE,
                'total' => $amount + $amount * Transaction::COMMISSION_FEE,
            ]);
        }
    }
}
