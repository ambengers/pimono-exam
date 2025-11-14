<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Transaction;

Broadcast::channel('transactions-channel', function ($user) {
    return $user !== null;
});

