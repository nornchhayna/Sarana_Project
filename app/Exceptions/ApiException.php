<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected $data;

    public function __construct(string $message = "API Error", int $code = 400, $data = null)
    {
        parent::__construct($message, $code);
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'data' => $this->data,
        ], $this->getCode());
    }
}
