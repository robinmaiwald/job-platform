<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use App\Repositories\JobRepository;

class JobController extends Controller
{
    public function __construct(private JobRepository $jobs)
    {
	//
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Job::class);

	return response()->json($this->jobs->all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Job::class);

	$validated = $request->validate([
	    'company_id' => ['required', 'exists:companies,id'],
	    'title' => ['required' , 'string', 'max:255'],
	    'description' => ['nullable', 'string'],
	    'location' => ['nullable', 'string', 'max:255'],
	]);

	$job = $this->jobs->create($validated, $request->user());

	return response()->json($job, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        $this->authorize('view', $job);

	return response()->json($this->jobs->find($job));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Job $job)
    {
        $this->authorize('update', $job);

	$validated = $request->validate([
	    'company_id' => ['required', 'exists:companies,id'],
	    'title' => ['required', 'string', 'max:255'],
	    'description' => ['nullable', 'string'],
	    'location' => ['nullable', 'string', 'max:255'],
	]);

	$job = $this->jobs->update($job, $validated);

	return response()->json($job);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);

	$this->jobs->delete($job);

	return response()->noContent();
    }
}
