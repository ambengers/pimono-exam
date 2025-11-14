<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'password' => Hash::make('password'),
            'balance' => 1000,
        ]);

        $receiver = User::create([
            'name' => 'Receiver',
            'email' => 'receiver@example.com',
            'password' => Hash::make('password'),
            'balance' => 0,
        ]);
    }
}
