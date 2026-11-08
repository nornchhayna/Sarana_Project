<?php

namespace App\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    protected $requiredPermission;

    public function __construct(string $message = "Unauthorized access", string $requiredPermission = null)
    {
        parent::__construct($message, 403);
        $this->requiredPermission = $requiredPermission;
    }

    public function getRequiredPermission()
    {
        return $this->requiredPermission;
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'required_permission' => $this->requiredPermission,
        ], 403);
    }
}
