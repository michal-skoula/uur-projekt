<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class PageBuilderException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        Log::error($this->getMessage(), $this->getTrace());
    }
}
