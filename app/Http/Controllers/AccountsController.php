<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\AccountResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class AccountsController extends Controller
{
    public function index(Request $request): JsonResource
    {
        $accounts = User::select('id', 'name', 'email')
            ->where(function ($query) use ($request) {
                $query->where('id', 'like', '%' . $request->search . '%')
                    ->orWhere('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            })
            ->where('id', '!=', Auth::id())
            ->paginate(50);

        return AccountResource::collection($accounts);
    }
}
