<?php

declare(strict_types=1);

namespace App\Services\Import\Exceptions;

class ImportFileReaderException extends \Exception
{
    protected $message = 'No Reader for the provided file';
}
