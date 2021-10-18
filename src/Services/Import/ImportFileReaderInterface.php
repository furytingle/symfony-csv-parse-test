<?php

declare(strict_types=1);

namespace App\Services\Import;

interface ImportFileReaderInterface
{
    /**
     * @param string $path
     * @return iterable
     */
    public function readFile(string $path): iterable;
}
