<?php

namespace App\Repositories\Contract;

use App\Models\Customer;

interface CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer;
    public function create(array $data): Customer;
    public function update(int $id, array $data): ?Customer;
}
