<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;

class PromotionApiController extends Controller
{
    public function index(): JsonResponse
    {
        $promotions = Promotion::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->latest()
            ->get()
            ->map(fn (Promotion $p) => $this->transformPromotion($p))
            ->values();

        return response()->json([
            'success' => true,
            'promotions' => $promotions,
            'data' => $promotions,
        ]);
    }

    public function show(string $idOrCode): JsonResponse
    {
        $promotion = Promotion::where('is_active', true)
            ->where(function ($q) use ($idOrCode) {
                $q->where('code', $idOrCode);
                if (is_numeric($idOrCode)) {
                    $q->orWhere('id', (int) $idOrCode);
                }
            })
            ->first();

        if (! $promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Promotion not found',
            ], 404);
        }

        $promoData = $this->transformPromotion($promotion);

        return response()->json([
            'success' => true,
            'promotion' => $promoData,
            'data' => $promoData,
        ]);
    }

    private function transformPromotion(Promotion $promotion): array
    {
        return [
            'id' => $promotion->id,
            'name' => $promotion->name,
            'title' => $promotion->name,
            'code' => $promotion->code,
            'description' => $promotion->description,
            'type' => $promotion->type,
            'value' => (float) $promotion->value,
            'discount_percent' => $promotion->type === 'percentage' ? (float) $promotion->value : null,
            'min_order_amount' => $promotion->min_order_amount ? (float) $promotion->min_order_amount : null,
            'max_discount_amount' => $promotion->max_discount_amount ? (float) $promotion->max_discount_amount : null,
            'is_active' => (bool) $promotion->is_active,
            'is_valid' => (bool) $promotion->is_valid,
            'starts_at' => $promotion->starts_at?->toIso8601String(),
            'ends_at' => $promotion->ends_at?->toIso8601String(),
        ];
    }
}
