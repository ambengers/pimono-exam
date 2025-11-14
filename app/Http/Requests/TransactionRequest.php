<?php

namespace App\Http\Requests;

use App\Models\Transaction;
use App\Rules\SenderHasSufficientFunds;
use Illuminate\Foundation\Http\FormRequest;
use App\Actions\Transactions\CreateTransaction;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'receiver' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:1', new SenderHasSufficientFunds],
        ];
    }

    public function persist(): Transaction
    {
        return (new CreateTransaction($this))->handle();
    }
}
