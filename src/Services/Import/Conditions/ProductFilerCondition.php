<?php

namespace App\Services\Import\Conditions;

use App\Entity\Product;

interface ProductFilerCondition
{
    public function pass(Product $product): bool;
}