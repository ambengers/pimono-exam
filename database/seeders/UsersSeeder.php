<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sender = User::create([
            'name' => 'Sender',
            'email' => 'sender@example.com',
            'balance' => 1000,
        ]);

        $receiver = User::create([
            'name' => 'Receiver',
            'email' => 'receiver@example.com',
            'balance' => 0,
        ]);
    }
}
