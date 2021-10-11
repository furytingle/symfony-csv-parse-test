<?php

declare(strict_types=1);

namespace App\Services\Import;

interface ImportProductsServiceInterface
{
    public function importFromCSV(string $path, bool $test = false): void;

    public function getRowsProcessed(): int;

    public function getRowsFiltered(): int;

    public function getRowsInvalid(): int;
}