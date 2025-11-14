<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'amount' => $this->amount,
            'commission_fee' => $this->commission_fee,
            'commission_fee_percentage' => $this->commission_fee_percentage,
            'total' => $this->total,
            'sender_balance_before' => $this->when($this->sender_id === $request->user()->getKey(), $this->sender_balance_before),
            'sender_balance_after' => $this->when($this->sender_id === $request->user()->getKey(), $this->sender_balance_after),
            'receiver_balance_before' => $this->when($this->receiver_id === $request->user()->getKey(), $this->receiver_balance_before),
            'receiver_balance_after' => $this->when($this->receiver_id === $request->user()->getKey(), $this->receiver_balance_after),
            'created_at' => $this->created_at,

            'sender' => AccountResource::make($this->whenLoaded('sender')),
            'receiver' => AccountResource::make($this->whenLoaded('receiver')),
        ];
    }
}
