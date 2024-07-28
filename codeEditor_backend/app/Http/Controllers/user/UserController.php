<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAllUsers()
    {
        $users = User::select('id', 'username', 'email', 'role')->get();

        return response()->json($users);
    }
}
