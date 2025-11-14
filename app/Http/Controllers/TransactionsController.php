<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $transactions = Transaction::with(['sender', 'receiver'])
            ->where('sender_id', Auth::id())
            ->orWhere('receiver_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return TransactionResource::collection($transactions);
    }

    public function store(TransactionRequest $request): JsonResource
    {
        return TransactionResource::make($request->persist());
    }
}
