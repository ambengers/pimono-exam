<?php

namespace App\Actions\Transactions;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\TransactionCreatedEvent;
use App\Http\Requests\TransactionRequest;
use App\Exceptions\SenderHasInsufficientFunds;

class CreateTransaction
{
    public function __construct(
        protected TransactionRequest $request,
    ) {}

    public function handle(): Transaction
    {
        return DB::transaction(function () {

            $sender = User::where('id', Auth::id())->lockForUpdate()->first();
            $receiver = User::where('id', $this->request->input('receiver'))->lockForUpdate()->first();
        
            $amount = $this->request->input('amount');
            $commission = $amount * Transaction::COMMISSION_FEE;

            $totalDebit = Transaction::getTotalDebit($amount);

            if ($sender->balance < $totalDebit) {
                throw new SenderHasInsufficientFunds();
            }

            $senderBalanceBefore = $sender->balance;
            $receiverBalanceBefore = $receiver->balance;
        
            $sender->balance -= $totalDebit;
            $receiver->balance += $amount;

            $senderBalanceAfter = $sender->balance;
            $receiverBalanceAfter = $receiver->balance;
        
            $sender->save();
            $receiver->save();
        
            $transaction = Transaction::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'amount' => $amount,
                'commission_fee' => $commission,
                'commission_fee_percentage' => Transaction::COMMISSION_FEE,
                'total' => $totalDebit,
                'sender_balance_before' => $senderBalanceBefore,
                'sender_balance_after' => $senderBalanceAfter,
                'receiver_balance_before' => $receiverBalanceBefore,
                'receiver_balance_after' => $receiverBalanceAfter,
            ]);

            event(
                new TransactionCreatedEvent(
                    $transaction->getKey(), 
                    $sender->getKey(),
                    $receiver->getKey(),
                    'Transaction created successfully'
                )
            );

            return $transaction;
        }, 5);
    }
}