<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerCreateRequest;
use App\Actions\Customer\CreateCustomerAction;
use App\Services\PriceCalculationService;

class CustomerController extends Controller
{
    protected PriceCalculationService $priceCalculationService;

    public function __construct(PriceCalculationService $priceCalculationService)
    {
        $this->priceCalculationService = $priceCalculationService;
    }

    public function store(CustomerCreateRequest $request)
    {
        $payload = $request->validated();

        // v2 uses enriched payload (example)
        $payload['external_reference'] = $request->header('X-External-Ref');

        $customer = (new CreateCustomerAction())->execute($payload);

        // Example: attach calculated default price for demo purposes
        $customer->default_price = $this->priceCalculationService->getEffectivePrice(1, $customer->id);

        return response()->json(['data' => $customer], 201);
    }
}
