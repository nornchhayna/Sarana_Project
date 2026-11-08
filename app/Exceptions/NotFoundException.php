<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    protected $resource;

    public function __construct(string $resource = "Resource", string $message = null)
    {
        $message = $message ?? "{$resource} not found.";
        parent::__construct($message, 404);
        $this->resource = $resource;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'resource' => $this->resource,
        ], 404);
    }
}
