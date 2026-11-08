<?php

namespace App\Exceptions;

use Exception;

class BusinessLogicException extends Exception
{
    protected $errors;

    public function __construct(string $message = "Business logic error", int $code = 422, array $errors = [])
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'errors' => $this->errors,
        ], $this->getCode());
    }
}
