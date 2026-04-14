<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function search(Request $request)
    {   
        $users = collect();
    
        if($request->filled('username')) {
            $users = User::where('name', 'like', '%' . $request->username . '%')
            ->where('id', '!=', auth()->id())
            ->get();
        }
    
        return view('users.search', compact('users'));
    }
    
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
}
