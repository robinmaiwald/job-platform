<?php

namespace App\Http\Controllers;

use App\Http\Requests\Companies\StoreCompanyRequest;
use App\Http\Requests\Companies\UpdateCompanyRequest;
use App\Repositories\CompanyRepository;
use App\Models\Company;

class CompanyController extends Controller
{

    public function __construct(private CompanyRepository $companies)
    {
        //
    }


    public function index()
    {
        $this->authorize('viewAny', Company::class);

	    return response()->json($this->companies->all());
    }


    public function create()
    {
        //
    }


    public function store(StoreCompanyRequest $request)
    {
        $this->authorize('create', Company::class);

        $validated = $request->validated();

	    $company = $this->companies->create($validated, $request->user());

	    return response()->json($company, 201);
    }


    public function show(Company $company)
    {
        $this->authorize('view', $company);

	    return response()->json($this->companies->find($company));
    }


    public function edit(Company $company)
    {
        //
    }


    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $this->authorize('update', $company);

        $validated = $request->validated();

        $company = $this->companies->update($company, $validated);

        return response()->json($company);
    }


    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);

        $this->companies->delete($company);

        return response()->noContent();
    }
}
