<?php

declare(strict_types=1);

namespace App\Services\Import\Exceptions;

class FileReadException extends \Exception
{
    protected $message = 'Failed to open file';
}
