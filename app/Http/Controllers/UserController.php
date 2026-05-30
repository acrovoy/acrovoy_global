<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function findByEmail(Request $request)
{
    $email = $request->get('email');

    $user = \App\Models\User::where('email', $email)->first();

    if (!$user) {
        return response()->json([
            'found' => false
        ]);
    }

    return response()->json([
        'found' => true,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'last_name' => $user->last_name,
            'email' => $user->email,
        ]
    ]);
}
}
