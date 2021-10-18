<?php

declare(strict_types=1);

namespace App\Services\Import;

interface ImportProductsServiceInterface
{
    /**
     * @param string $path
     * @param bool $test
     */
    public function importFromFile(string $path, bool $test = false): void;

    /**
     * @return int
     */
    public function getRowsProcessed(): int;

    /**
     * @return int
     */
    public function getRowsFiltered(): int;

    /**
     * @return int
     */
    public function getRowsInvalid(): int;
}
