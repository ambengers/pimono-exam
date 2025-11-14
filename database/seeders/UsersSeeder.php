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
        $sender = User::factory()->create([
            'name' => 'Bruce Wayne',
            'email' => 'sender@example.com',
            'balance' => 100000000,
        ]);

        $receiver = User::factory()->create([
            'name' => 'Clark Kent',
            'email' => 'receiver@example.com',
            'balance' => 1000000,
        ]);

        User::factory()
            ->count(100)
            ->create()
            ->each(function ($user) {
                $user->balance = fake()->randomFloat(2, 0, 10000);
                $user->save();
            });
    }
}
