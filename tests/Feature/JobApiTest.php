<?php

use App\Models\Job;

test('guest can list jobs', function () {

    $job = Job::factory()->create();

    $response = $this->getJson('/api/jobs');

    $response->assertOk();

    $response->assertJsonFragment([
	'id' => $job->id,
	'title' => $job->title,
    ]);
});
