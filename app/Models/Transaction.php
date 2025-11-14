<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    const COMMISSION_FEE = 0.015;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'amount',
        'commission_fee',
        'commission_fee_percentage',
        'total',
        'sender_balance_before',
        'sender_balance_after',
        'receiver_balance_before',
        'receiver_balance_after',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'commission_fee' => 'decimal:4',
        'commission_fee_percentage' => 'decimal:4',
        'total' => 'decimal:4',
        'sender_balance_before' => 'decimal:4',
        'sender_balance_after' => 'decimal:4',
        'receiver_balance_before' => 'decimal:4',
        'receiver_balance_after' => 'decimal:4',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public static function getTotalDebit(float $amount): float
    {
        return $amount + $amount * self::COMMISSION_FEE;
    }
}
