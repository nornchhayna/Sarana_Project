# Custom Exception Usage Examples

This file demonstrates how to use the custom exception classes in your controllers.

## 1. NotFoundException

Use when a resource doesn't exist:

```php
use App\Exceptions\NotFoundException;

public function show($id)
{
    $product = Product::find($id);

    if (!$product) {
        throw new NotFoundException('Product', "Product with ID {$id} not found");
    }

    return response()->json(['success' => true, 'data' => $product]);
}
```

Response:
```json
{
    "success": false,
    "message": "Product with ID 123 not found",
    "resource": "Product"
}
```

## 2. BusinessLogicException

Use for business rule violations:

```php
use App\Exceptions\BusinessLogicException;

public function updateStock(Request $request, $id)
{
    $product = Product::find($id);

    if ($product->stock < $request->quantity) {
        throw new BusinessLogicException(
            'Insufficient stock',
            422,
            ['available' => $product->stock, 'requested' => $request->quantity]
        );
    }

    $product->stock -= $request->quantity;
    $product->save();

    return response()->json(['success' => true, 'data' => $product]);
}
```

Response:
```json
{
    "success": false,
    "message": "Insufficient stock",
    "errors": {
        "available": 5,
        "requested": 10
    }
}
```

## 3. UnauthorizedException

Use for permission/authorization issues:

```php
use App\Exceptions\UnauthorizedException;

public function delete($id)
{
    if (!auth()->user()->can('delete-products')) {
        throw new UnauthorizedException(
            'You do not have permission to delete products',
            'delete-products'
        );
    }

    Product::destroy($id);

    return response()->json(['success' => true, 'message' => 'Product deleted']);
}
```

Response:
```json
{
    "success": false,
    "message": "You do not have permission to delete products",
    "required_permission": "delete-products"
}
```

## 4. ApiException

Use for general API errors with custom data:

```php
use App\Exceptions\ApiException;

public function processPayment(Request $request)
{
    $paymentResult = PaymentGateway::process($request->all());

    if (!$paymentResult->success) {
        throw new ApiException(
            'Payment processing failed',
            400,
            [
                'gateway_code' => $paymentResult->code,
                'gateway_message' => $paymentResult->message
            ]
        );
    }

    return response()->json(['success' => true, 'data' => $paymentResult]);
}
```

Response:
```json
{
    "success": false,
    "message": "Payment processing failed",
    "data": {
        "gateway_code": "CARD_DECLINED",
        "gateway_message": "Insufficient funds"
    }
}
```

## 5. Creating Your Own Custom Exception

You can create additional custom exceptions by extending the base Exception class:

```php
<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    protected $currentBalance;
    protected $requiredAmount;

    public function __construct(float $currentBalance, float $requiredAmount)
    {
        $this->currentBalance = $currentBalance;
        $this->requiredAmount = $requiredAmount;
        $message = "Insufficient balance. Required: {$requiredAmount}, Available: {$currentBalance}";
        parent::__construct($message, 400);
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'current_balance' => $this->currentBalance,
            'required_amount' => $this->requiredAmount,
        ], 400);
    }
}
```

Usage:
```php
use App\Exceptions\InsufficientBalanceException;

public function transfer(Request $request)
{
    $user = auth()->user();

    if ($user->balance < $request->amount) {
        throw new InsufficientBalanceException($user->balance, $request->amount);
    }

    // Process transfer...
}
```

## Best Practices

1. **Use specific exceptions**: Choose the most appropriate exception type for your error
2. **Provide meaningful messages**: Error messages should be clear and actionable
3. **Include relevant data**: Pass additional context data that helps debug the issue
4. **Use appropriate HTTP status codes**:
   - 400: Bad Request (client error)
   - 401: Unauthorized (authentication required)
   - 403: Forbidden (permission denied)
   - 404: Not Found (resource doesn't exist)
   - 422: Unprocessable Entity (validation/business logic error)
   - 500: Internal Server Error (server error)
