<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{

  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required|string|email|max:255',
      'password' => 'required|string|min:4',
    ]);

    $client = $request->get('client');
    $user = $client->users()->where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      return response()->json(['message' => 'Unauthorized'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json(['token' => $token]);
  }
}