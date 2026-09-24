<?php

namespace App\Http\Controllers;

use App\Http\Requests\Jobs\StoreJobRequest;
use App\Http\Requests\Jobs\UpdateJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Repositories\JobRepository;


class JobController extends Controller
{
    public function __construct(private JobRepository $jobs)
    {
	    //
    }

    public function index()
    {
        $this->authorize('viewAny', Job::class);

	    return JobResource::collection(
            $this->jobs->all()
        );
    }

    public function create()
    {
        //
    }

    public function store(StoreJobRequest $request)
    {
        $this->authorize('create', Job::class);

        $validated = $request->validated();

        $job = $this->jobs->create($validated, $request->user());

        return (new JobResource($job))->response()->setStatusCode(201); 
    }

    public function show(Job $job)
    {
        $this->authorize('view', $job);

	    return new JobResource($this->jobs->find($job));
    }

    public function edit(Job $job)
    {
        //
    }

    public function update(UpdateJobRequest $request, Job $job)
    {
        $this->authorize('update', $job);

        $validated = $request->validated();


        $job = $this->jobs->update($job, $validated);

        return new JobResource($job);
    }

    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);

        $this->jobs->delete($job);

        return response()->noContent();
    }
}
