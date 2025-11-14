<?php

namespace App\Rules;

use Closure;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\ValidationRule;

class SenderHasSufficientFunds implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $sender = Auth::user();

        $totalDebit = Transaction::getTotalDebit($value);

        if ($sender->balance < $totalDebit) {
            $fail('Insufficient funds.');
        }
    }
}
