<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerCreateRequest;
use App\Actions\Customer\CreateCustomerAction;

class CustomerController extends Controller
{
    /**
     * Create a customer (thin controller: orchestrates only).
     */
    public function store(CustomerCreateRequest $request)
    {
        $customer = (new CreateCustomerAction())->execute($request->validated());

        return response()->json([
            'data' => $customer,
        ], 201);
    }

    public function show($customerId)
    {
        // Example: use repository pattern for retrieval
        $repository = app(\App\Repositories\Contract\CustomerRepositoryInterface::class);
        $customer = $repository->findById((int) $customerId);

        if (! $customer) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['data' => $customer]);
    }
}
