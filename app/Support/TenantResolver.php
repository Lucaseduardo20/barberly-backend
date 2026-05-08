<?php

namespace App\Support;

use App\Models\Company;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TenantResolver
{
    public function resolveCompany(Request $request): Company
    {
        if ($request->user()?->company) {
            return $request->user()->company;
        }

        $companyId = $request->input('company_id') ?: $request->header('X-Company-Id');

        if ($companyId) {
            return Company::query()->findOrFail($companyId);
        }

        $company = Company::query()->first();

        if (!$company) {
            throw new HttpException(422, 'Nenhuma empresa cadastrada para resolver o tenant.');
        }

        return $company;
    }

    public function resolveCompanyId(Request $request): int
    {
        return $this->resolveCompany($request)->id;
    }
}
