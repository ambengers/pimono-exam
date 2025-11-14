<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class AccountsController extends Controller
{
    public function index(Request $request)
    {
        $accounts = User::select('id', 'name', 'email')
            ->where('id', 'like', '%' . $request->search . '%')
            ->orWhere('email', 'like', '%' . $request->search . '%')
            ->paginate(50);

        return UserResource::collection($accounts);
    }
}
