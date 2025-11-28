<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function signIn() {
        return view('auth.signin');
    }

    public function registr(Request $request) {
        $user = $request->validate([
            'name'=>'required',
            'email'=>'email|required',
            'password'=>'required|min:6'
        ]);
        return response()->json($user);
    }
}
