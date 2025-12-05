<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PriceCalculationService
{
    /**
     * Return effective price for a product and customer.
     * Cache result for short TTL to reduce DB pressure on price lookups.
     */
    public function getEffectivePrice(int $productId, int $customerId): float
    {
        $cacheKey = "pricing:product:$productId:customer:$customerId";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($productId, $customerId) {
            return $this->calculatePrice($productId, $customerId);
        });
    }

    protected function calculatePrice(int $productId, int $customerId): float
    {
        // Simplified: in real world apply discounts, tiers, overrides
        return 99.0;
    }
}
