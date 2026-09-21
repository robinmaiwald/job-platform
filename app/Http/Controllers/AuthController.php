<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
	$validated = $request->validate([
	    'email' => ['required', 'email'],
	    'password' => [ 'required', 'string'],
	]);

	$user = User::where('email', $validated['email'])->first();

	if (! $user || ! Hash::check($validated['password'], $user->password)) {
	    throw ValidationException::withMessages([
		'email' => ['The provided data are incorrect.'],
	    ]);
	}

	$token = $user->createToken('api-token')->plainTextToken;

	return response()->json([
	    'token' => $token,
	]);
    }
}
