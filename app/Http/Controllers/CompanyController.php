<?php

namespace App\Http\Controllers;

use App\Repositories\CompanyRepository;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function __construct(private CompanyRepository $companies)
    {
        //
    }

    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $this->authorize('viewAny', Company::class);

	return response()->json($this->companies->all());
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
        $this->authorize('create', Company::class);

	$validated = $request->validate([
	    'name' => ['required', 'string', 'max:255'],
	    'description' => ['nullable', 'string'],
	    'website' => ['nullable', 'url', 'max:255'],
	]);

	$company = $this->companies->create($validated, $request->user());

	return response()->json($company, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        $this->authorize('view', $company);

	return response()->json($this->companies->find($company));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company)
    {
        $this->authorize('update', $company);

	$validated = $request->validate([
	    'name' => ['required', 'string', 'max:255'],
	    'description' => ['nullable', 'string'],
	    'website' => ['nullable', 'url', 'max:255'],
	]);

	$company = $this->companies->update($company, $validated);

	return response()->json($company);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);

	$this->companies->delete($company);

	return response()->noContent();
    }
}
