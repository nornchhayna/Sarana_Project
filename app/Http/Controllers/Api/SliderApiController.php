<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;

class SliderApiController extends Controller
{
    public function index(): JsonResponse
    {
        $sliders = Slider::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function (Slider $slider) {
                $imageUrl = null;
                if (! empty($slider->image)) {
                    $imageUrl = str_starts_with($slider->image, 'http')
                        ? $slider->image
                        : url('storage/' . ltrim($slider->image, '/'));
                }

                return [
                    'id' => $slider->id,
                    'title' => $slider->title,
                    'subtitle' => $slider->subtitle ?? null,
                    'description' => $slider->subtitle ?? null,
                    'badge_text' => $slider->badge_text ?? null,
                    'image' => $slider->image,
                    'image_url' => $imageUrl,
                    'button_text' => $slider->button_text ?? null,
                    'button_url' => $slider->button_url ?? null,
                    'link' => $slider->button_url ?? null,
                    'sort_order' => $slider->sort_order ?? 0,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'sliders' => $sliders,
            'data' => $sliders,
        ]);
    }
}
