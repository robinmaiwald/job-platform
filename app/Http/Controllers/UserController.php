<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;

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

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validated();

        $user = $this->users->create($validated);

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return new UserResource($this->users->find($user));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validated();

        $user = $this->users->update($user, $validated);

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $this->users->delete($user);

        return response()->noContent();
    }
}