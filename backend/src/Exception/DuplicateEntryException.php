<?php

namespace App\Exception;

class DuplicateEntryException extends \Exception
{
    public function __construct(
        string $message = 'Duplicate entry.',
    ) {
        parent::__construct($message);
    }
}