<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PromoCodeApiController extends Controller
{
    use ApiResponse;

    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $promo = PromoCode::where('code', $request->code)->first();

        if (! $promo || ! $promo->isValid()) {
            return $this->error('Invalid or expired promo code.', 422);
        }

        $discount = $promo->calculateDiscount((float) $request->subtotal);

        return $this->success([
            'code' => $promo->code,
            'type' => $promo->type,
            'value' => $promo->value,
            'discount' => $discount,
            'final_total' => max(0, $request->subtotal - $discount),
        ]);
    }
}
