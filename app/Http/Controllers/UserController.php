<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
	$validated = $request->validate([
	'name' => ['required', 'string', 'max:255'],
	'email' => ['required', 'email', 'unique:users,email'],
	'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = User::create($validated);

    return response()->json($user, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

	return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

	$validated = $request->validate([
	    'name' => ['sometimes', 'string', 'max:255'],
	    'email' => ['sometimes', 'email', 'unique:users,email,' . $user->id],
	    'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
	]);

	$user->update($validated);

	return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
       $this->authorize('delete',$user);

	if ($user->jobs()->exists())
	{
	    return response()->json(
	    [
		'message' => 'You cannot delete your account while you have jobs.'
	    ],409);
    }

	$user->delete();

	return response()->noContent();
    }
}

