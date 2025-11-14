<?php

namespace App\Actions\Transactions;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
        
            $sender->balance -= $totalDebit;
            $receiver->balance += $amount;
        
            $sender->save();
            $receiver->save();
        
            return Transaction::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'amount' => $amount,
                'commission_fee' => $commission,
                'total' => $totalDebit,
            ]);
        
        }, 3);
    }
}