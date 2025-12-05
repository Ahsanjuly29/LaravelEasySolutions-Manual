<?php

namespace App\Actions\Customer;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CreateCustomerAction
{
    /**
     * Execute the creation steps for a new customer.
     * Single responsibility: create customer and trigger post-create events.
     */
    public function execute(array $payload): Customer
    {
        return DB::transaction(function () use ($payload) {
            $customer = Customer::create([
                'name' => $payload['name'],
                'email' => $payload['email'] ?? null,
                'phone' => $payload['phone'] ?? null,
            ]);

            // Keep domain events and other side effects as Jobs
            if (! empty($payload['imported_by'])) {
                // e.g. dispatch job to notify or log import
            }

            return $customer;
        });
    }
}
