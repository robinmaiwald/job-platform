<?php


use App\Models\User;

test('guest can register', function () {

	$response = $this->postJson('/api/users', [
	    'name' => 'Bob',
	    'email' => 'bob@example.com',
	    'password' => 'password',
	    'password_confirmation' => 'password',
	]);

	$response->assertCreated();

	$response->assertJsonFragment([
	    'name' => 'Bob',
	    'email' => 'bob@example.com',
	]);

	$this->assertDatabaseHas('users', [
	    'email' => 'bob@example.com',
	]);
});
