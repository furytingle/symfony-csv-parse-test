<?php

declare(strict_types=1);

namespace App\Services\Import;

interface ImportFileReaderInterface
{
    public function readFile(string $path): iterable;
}
