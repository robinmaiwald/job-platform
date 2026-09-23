<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UserRepository $users
    ) {
        //
    }

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $this->users->create($validated);

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return response()->json(
            $this->users->find($user)
        );
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'email',
                'unique:users,email,' . $user->id,
            ],
            'password' => [
                'sometimes',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user = $this->users->update($user, $validated);

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $this->users->delete($user);

        return response()->noContent();
    }
}