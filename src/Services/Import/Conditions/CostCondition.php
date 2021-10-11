<?php

declare(strict_types=1);

namespace App\Services\Import\Conditions;

use App\Entity\Product;

class CostCondition implements ProductFilerCondition
{
    public const MAX_COST = 1000;

    public function pass(Product $product): bool
    {
        return (float)$product->getCost() < self::MAX_COST;
    }
}