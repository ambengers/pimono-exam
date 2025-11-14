<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    const COMMISSION_FEE = 0.015;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'amount',
        'commission_fee',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_fee' => 'decimal:2',
    ];

    protected function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    protected function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public static function getTotalDebit(float $amount): float
    {
        return $amount + $amount * self::COMMISSION_FEE;
    }
}
