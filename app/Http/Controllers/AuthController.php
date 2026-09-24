<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(UserLoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = $user->createToken(
            'api-token',
            ['user']
        )->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }

    public function adminLogin(AdminLoginRequest $request)
    {
        $validated = $request->validated();

        $admin = Admin::where('name', $validated['name'])->first();

        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = $admin->createToken(
            'admin',
            ['admin']
        )->plainTextToken;

        return response()->json([
            'admin' => $admin,
            'token' => $token,
        ]);
    }
}
