<?php

namespace App\Http\Controllers;

use App\Http\Requests\Companies\StoreCompanyRequest;
use App\Http\Requests\Companies\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Repositories\CompanyRepository;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{

    public function __construct(private CompanyRepository $companies)
    {
        //
    }


    public function index()
    {
        $this->authorize('viewAny', Company::class);

        return CompanyResource::collection($this->companies->all());
    }


    public function store(StoreCompanyRequest $request)
    {
        $this->authorize('create', Company::class);

        $validated = $request->validated();

	    $company = $this->companies->create($validated, $request->user());

	    return (new CompanyResource($company))->response()->setStatusCode(201);
    }


    public function show(Company $company)
    {
        $this->authorize('view', $company);

	    return new CompanyResource($this->companies->find($company));
    }


    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $this->authorize('update', $company);

        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $oldLogo = $company->logo;

            $validated['logo'] = $request->file('logo')->store('company-logos', 'public');

            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
        }

        $company = $this->companies->update($company, $validated);

        return new CompanyResource($company);
    }


    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);

        $this->companies->delete($company);

        return response()->noContent();
    }
}
