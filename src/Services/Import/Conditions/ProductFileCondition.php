<?php

declare(strict_types=1);

namespace App\Services\Import\Conditions;

use App\Entity\Product;

interface ProductFileCondition
{
    /**
     * @param Product $product
     * @return bool
     */
    public function pass(Product $product): bool;
}
