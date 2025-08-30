<?php

namespace App\Http\Controllers\Api;

use App\Actions\ModelApi\FilterModel;
use App\Actions\ModelApi\FilterModels;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = Company::paginate(10);
            return successResponse('Showing All Data', $data);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyRequest $request)
    {
        try {
            $allData = Company::create($request->validated());
            return successResponse('This data has been Created', $allData);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = Company::find($id);
            if (empty($data)) {
                throw new \Exception('Unable to Find This Task');
            }

            $data['url'] = route('api-company-data.update', $id);

            return successResponse('Open Modal', $data);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    public function edit($id)
    {
        return $this->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, string $id)
    {
        try {
            $taskData = Company::find($id);
            if (empty($taskData)) {
                throw new \Exception('Unable to find this data');
            }

            $taskData->update($request->validated());

            return successResponse('This data has been Updated', $taskData);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $ids = !is_array($request->ids) ? explode(',', $request->ids) : $request->ids;
            Company::whereIn('id', $ids)->delete();
            return successResponse('This data has been Destroyed');
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }
}
