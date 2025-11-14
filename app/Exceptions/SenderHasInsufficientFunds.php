<?php

namespace App\Exceptions;

use Exception;

class SenderHasInsufficientFunds extends Exception
{
    public function render()
    {
        return response()->json([
            'message' => 'Account has insufficient funds to make this transaction.',
            'errors' => [
                'amount' => 'Account has insufficient funds to make this transaction.',
            ],
        ], 422);
    }
}
