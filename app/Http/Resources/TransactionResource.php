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
            'total' => $this->total,
            'created_at' => $this->created_at,

            'sender' => AccountResource::make($this->whenLoaded('sender')),
            'receiver' => AccountResource::make($this->whenLoaded('receiver')),
        ];
    }
}
