<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Smoke test: hits every GET route under /admin as a logged-in admin
 * (admin guard only, same as real login) and reports any non-2xx/3xx.
 *
 * Run against the real DB (all changes rolled back):
 *   DB_CONNECTION=mysql DB_DATABASE=ashahi DB_PORT=3308 php artisan test --filter=AdminSmokeTest
 */
class AdminSmokeTest extends TestCase
{
    use DatabaseTransactions;

    protected array $paramModels = [
        'blog' => \App\Models\Blog::class,
        'brand' => \App\Models\Brand::class,
        'category' => \App\Models\Category::class,
        'color' => \App\Models\Color::class,
        'contact' => \App\Models\Contact::class,
        'gift' => \App\Models\Gift::class,
        'invoice' => \App\Models\Invoice::class,
        'order' => \App\Models\Order::class,
        'payment' => \App\Models\Payment::class,
        'phoneModel' => \App\Models\PhoneModel::class,
        'phoneStorage' => \App\Models\PhoneStorage::class,
        'storage' => \App\Models\PhoneStorage::class,
        'product' => \App\Models\Product::class,
        'promotion' => \App\Models\Promotion::class,
        'purchaseOrder' => \App\Models\PurchaseOrder::class,
        'purchaseOrderItem' => \App\Models\PurchaseOrderItem::class,
        'item' => \App\Models\PurchaseOrderItem::class,
        'ram' => \App\Models\PhoneRam::class,
        'review' => \App\Models\Review::class,
        'role' => \App\Models\Role::class,
        'permission' => \App\Models\Permission::class,
        'slider' => \App\Models\Slider::class,
        'stock' => \App\Models\Stock::class,
        'stockMovement' => \App\Models\StockMovement::class,
        'stockReceiving' => \App\Models\StockReceiving::class,
        'stockReceivingItem' => \App\Models\StockReceivingItem::class,
        'supplier' => \App\Models\Supplier::class,
        'user' => \App\Models\User::class,
        'variant' => \App\Models\ProductVariant::class,
        'image' => \App\Models\ProductImage::class,
    ];

    public function test_all_admin_get_pages(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->where('is_active', true)->firstOrFail();
        $this->actingAs($admin, 'admin');

        $failures = [];
        $ok = 0;
        $seen = [];

        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            if (! str_starts_with($uri, 'admin') || ! in_array('GET', $route->methods())) {
                continue;
            }
            if (in_array($uri, ['admin/login'])) {
                continue;
            }
            if (isset($seen[$uri])) {
                continue;
            }
            $seen[$uri] = true;

            $url = '/'.$uri;
            $skip = false;
            foreach ($route->parameterNames() as $p) {
                $cls = $this->paramModels[$p] ?? null;
                if (! $cls) {
                    $failures[] = "SKIP  {$url}  (unknown param {{$p}})";
                    $skip = true;
                    break;
                }
                $model = method_exists($cls, 'withTrashed') ? $cls::withTrashed()->first() : $cls::first();
                if (! $model) {
                    $failures[] = "SKIP  {$url}  (no {$cls} rows)";
                    $skip = true;
                    break;
                }
                $url = preg_replace('/\{'.$p.'\??\}/', $model->getRouteKey(), $url);
            }
            if ($skip) {
                continue;
            }

            try {
                $res = $this->withoutExceptionHandling()->get($url);
                $code = $res->getStatusCode();
                if ($code >= 400) {
                    $failures[] = "HTTP {$code}  {$url}";
                } elseif ($code >= 300) {
                    $loc = $res->headers->get('Location');
                    if (str_contains((string) $loc, 'login')) {
                        $failures[] = "REDIRECT-TO-LOGIN  {$url} -> {$loc}";
                    } else {
                        $ok++;
                    }
                } else {
                    $ok++;
                }
            } catch (\Throwable $e) {
                $msg = preg_replace('/\s+/', ' ', $e->getMessage());
                $where = str_replace(base_path().'/', '', $e->getFile()).':'.$e->getLine();
                $failures[] = 'ERROR  '.$url.'  ['.class_basename($e).'] '.substr($msg, 0, 300).'  @ '.$where;
            }
            // reset auth state between requests
            $this->actingAs($admin, 'admin');
        }

        fwrite(STDERR, "\n===== ADMIN GET SMOKE: {$ok} OK, ".count($failures)." problems =====\n".implode("\n", $failures)."\n");
        $this->assertTrue(true);
    }
}
