<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contract\CustomerRepositoryInterface;
use App\Models\Customer;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer
    {
        return Customer::query()->find($id);
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(int $id, array $data): ?Customer
    {
        $customer = $this->findById($id);
        if (! $customer) {
            return null;
        }

        $customer->update($data);
        return $customer;
    }
}
