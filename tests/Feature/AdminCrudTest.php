<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Exercises create / update / delete / toggle actions across the admin panel.
 * Run against real DB (rolled back):
 *   DB_CONNECTION=mysql DB_DATABASE=ashahi DB_PORT=3308 php artisan test --filter=AdminCrudTest
 */
class AdminCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected array $log = [];
    protected int $ok = 0;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->admin = User::where('role', 'admin')->where('is_active', true)->firstOrFail();
    }

    protected function img(string $name = 'x.png'): UploadedFile
    {
        return UploadedFile::fake()->image($name, 50, 50);
    }

    /** Fire a request and record the outcome. Returns the response or null. */
    protected function hit(string $label, string $method, string $url, array $data = [])
    {
        $this->actingAs($this->admin, 'admin');
        try {
            $res = $this->withoutExceptionHandling()->from('/admin')->call(strtoupper($method), $url, $data, [], array_filter($data, fn ($v) => $v instanceof UploadedFile || (is_array($v) && isset($v[0]) && $v[0] instanceof UploadedFile)), ['HTTP_ACCEPT' => 'text/html']);
            $code = $res->getStatusCode();
            $errors = session('errors');
            if ($code >= 400) {
                $this->log[] = "FAIL  {$label}  HTTP {$code}";
            } elseif ($errors && $errors->any()) {
                $this->log[] = "FAIL  {$label}  validation: ".implode(' | ', $errors->all());
            } elseif (session('error')) {
                $this->log[] = "WARN  {$label}  flash error: ".session('error');
                $this->ok++;
            } else {
                $this->ok++;
            }

            return $res;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->log[] = "FAIL  {$label}  validation: ".implode(' | ', \Illuminate\Support\Arr::flatten($e->errors()));
        } catch (\Throwable $e) {
            $msg = preg_replace('/\s+/', ' ', $e->getMessage());
            $where = str_replace(base_path().'/', '', $e->getFile()).':'.$e->getLine();
            $this->log[] = "ERROR {$label}  [".class_basename($e).'] '.substr($msg, 0, 260)."  @ {$where}";
        } finally {
            session()->forget(['errors', 'error', 'success', 'status']);
        }

        return null;
    }

    public function test_admin_crud(): void
    {
        $t = uniqid();
        $brand = Brand::first();
        $cat = Category::first();
        $product = Product::whereHas('variants')->first();
        $variant = ProductVariant::first();
        $color = Color::first();
        $supplier = Supplier::first();

        // ── Categories
        $this->hit('category.store', 'POST', '/admin/categories', ['name' => "Cat $t", 'description' => 'd', 'image' => $this->img(), 'is_active' => 1, 'sort_order' => 1]);
        $c = Category::where('name', "Cat $t")->first();
        if ($c) {
            $this->hit('category.update', 'PUT', "/admin/categories/{$c->id}", ['name' => "Cat2 $t", 'is_active' => 1, 'image' => $this->img()]);
            $this->hit('category.destroy', 'DELETE', "/admin/categories/{$c->id}");
        } else { $this->log[] = 'FAIL  category not created'; }

        // ── Brands
        $this->hit('brand.store', 'POST', '/admin/brands', ['name' => "Brand $t", 'logo' => $this->img(), 'is_active' => 1]);
        $b = Brand::where('name', "Brand $t")->first();
        if ($b) {
            $this->hit('brand.update', 'PUT', "/admin/brands/{$b->id}", ['name' => "Brand2 $t", 'is_active' => 1]);
            $this->hit('brand.toggle', 'PATCH', "/admin/brands/{$b->id}/toggle-status");
            $this->hit('brand.destroy', 'DELETE', "/admin/brands/{$b->id}");
        } else { $this->log[] = 'FAIL  brand not created'; }

        // ── Colors
        $this->hit('color.store', 'POST', '/admin/colors', ['name' => "Color $t", 'hex_code' => '#112233', 'is_active' => 1]);
        $co = Color::where('name', "Color $t")->first();
        if ($co) {
            $this->hit('color.update', 'PUT', "/admin/colors/{$co->id}", ['name' => "Color2 $t", 'hex_code' => '#445566', 'is_active' => 1]);
            $this->hit('color.destroy', 'DELETE', "/admin/colors/{$co->id}");
        } else { $this->log[] = 'FAIL  color not created'; }

        // ── Phone models
        $this->hit('model.store', 'POST', '/admin/models', ['brand_id' => $brand->id, 'name' => "Model $t", 'is_active' => 1]);
        $m = \App\Models\PhoneModel::where('name', "Model $t")->first();
        if ($m) {
            $this->hit('model.update', 'PUT', "/admin/models/{$m->id}", ['brand_id' => $brand->id, 'name' => "Model2 $t", 'is_active' => 1]);
            $this->hit('model.destroy', 'DELETE', "/admin/models/{$m->id}");
        } else { $this->log[] = 'FAIL  model not created'; }

        // ── Storages
        $cap = 'T'.substr($t, -6);
        $this->hit('storage.store', 'POST', '/admin/storages', ['capacity' => $cap, 'is_active' => 1]);
        $s = \App\Models\PhoneStorage::where('capacity', $cap)->first();
        if ($s) {
            $before = \App\Models\PhoneStorage::withTrashed()->count();
            $this->hit('storage.update', 'PUT', "/admin/storages/{$s->id}", ['capacity' => $cap.'X', 'is_active' => 1]);
            if (! \App\Models\PhoneStorage::where('capacity', $cap.'X')->where('id', $s->id)->exists()) {
                $this->log[] = 'BUG   storage.update did not update the record (rows before '.$before.', after '.\App\Models\PhoneStorage::withTrashed()->count().')';
            }
            $this->hit('storage.destroy', 'DELETE', "/admin/storages/{$s->id}");
            if (\App\Models\PhoneStorage::whereKey($s->id)->exists()) {
                $this->log[] = 'BUG   storage.destroy did not delete the record';
            }
        } else { $this->log[] = 'FAIL  storage not created'; }

        // ── RAMs
        $this->hit('ram.store', 'POST', '/admin/rams', ['capacity' => $cap, 'label' => 'x', 'is_active' => 1]);
        $r = \App\Models\PhoneRam::where('capacity', $cap)->first();
        if ($r) {
            $this->hit('ram.update', 'PUT', "/admin/rams/{$r->id}", ['capacity' => $cap.'Y', 'is_active' => 1]);
            $this->hit('ram.destroy', 'DELETE', "/admin/rams/{$r->id}");
        } else { $this->log[] = 'FAIL  ram not created'; }

        // ── Blogs
        $this->hit('blog.store', 'POST', '/admin/blogs', ['title' => "Blog $t", 'content' => 'hello world', 'image' => $this->img('a.jpg'), 'is_published' => 1]);
        $bl = \App\Models\Blog::where('title', "Blog $t")->first();
        if ($bl) {
            $this->hit('blog.update', 'PUT', "/admin/blogs/{$bl->id}", ['title' => "Blog2 $t", 'content' => 'x', 'is_published' => 0]);
            $this->hit('blog.destroy', 'DELETE', "/admin/blogs/{$bl->id}");
        } else { $this->log[] = 'FAIL  blog not created'; }

        // ── Sliders
        $this->hit('slider.store', 'POST', '/admin/sliders', ['title' => "Slide $t", 'image' => $this->img(), 'button_url' => 'https://example.com', 'is_active' => 1, 'sort_order' => 1]);
        $sl = \App\Models\Slider::where('title', "Slide $t")->first();
        if ($sl) {
            $this->hit('slider.update', 'PUT', "/admin/sliders/{$sl->id}", ['title' => "Slide2 $t", 'is_active' => 1]);
            $this->hit('slider.toggle', 'PATCH', "/admin/sliders/{$sl->id}/toggle-status");
            $this->hit('slider.reorder', 'POST', '/admin/sliders/reorder', ['order' => [$sl->id], 'ids' => [$sl->id]]);
            $this->hit('slider.destroy', 'DELETE', "/admin/sliders/{$sl->id}");
        } else { $this->log[] = 'FAIL  slider not created'; }

        // ── Promotions
        $this->hit('promotion.store', 'POST', '/admin/promotions', ['name' => "Promo $t", 'code' => "P$t", 'type' => 'percentage', 'value' => 10, 'is_active' => 1, 'starts_at' => now()->toDateString(), 'expires_at' => now()->addMonth()->toDateString()]);
        $p = \App\Models\Promotion::where('code', "P$t")->first();
        if ($p) {
            $this->hit('promotion.update', 'PUT', "/admin/promotions/{$p->id}", ['name' => "Promo2 $t", 'code' => "P$t", 'type' => 'fixed', 'value' => 5, 'is_active' => 1]);
            $this->hit('promotion.toggle', 'PATCH', "/admin/promotions/{$p->id}/toggle");
            $this->hit('promotion.destroy', 'DELETE', "/admin/promotions/{$p->id}");
        } else { $this->log[] = 'FAIL  promotion not created'; }

        // ── Gifts
        $this->hit('gift.store', 'POST', '/admin/gifts', ['name' => "Gift $t", 'image' => $this->img(), 'is_active' => 1]);
        $g = \App\Models\Gift::where('name', "Gift $t")->first();
        if ($g) {
            $this->hit('gift.update', 'PUT', "/admin/gifts/{$g->id}", ['name' => "Gift2 $t", 'is_active' => 1]);
            $this->hit('gift.destroy', 'DELETE', "/admin/gifts/{$g->id}");
        } else { $this->log[] = 'FAIL  gift not created'; }

        // ── Suppliers
        $this->hit('supplier.store', 'POST', '/admin/suppliers', ['name' => "Sup $t", 'email' => "s$t@x.com", 'is_active' => 1]);
        $su = Supplier::where('name', "Sup $t")->first();
        if ($su) {
            $this->hit('supplier.update', 'PUT', "/admin/suppliers/{$su->id}", ['name' => "Sup2 $t", 'is_active' => 1]);
            $this->hit('supplier.toggle', 'PATCH', "/admin/suppliers/{$su->id}/toggle-active");
            $this->hit('supplier.destroy', 'DELETE', "/admin/suppliers/{$su->id}");
            $this->hit('supplier.restore', 'PATCH', "/admin/suppliers/{$su->id}/restore");
        } else { $this->log[] = 'FAIL  supplier not created'; }

        // ── Roles & permissions
        $this->hit('permission.store', 'POST', '/admin/permissions', ['name' => "Perm $t", 'group' => 'Test']);
        $pe = \App\Models\Permission::where('name', "Perm $t")->first();
        if ($pe) {
            $this->hit('permission.update', 'PUT', "/admin/permissions/{$pe->id}", ['name' => "Perm2 $t", 'slug' => "perm.$t", 'group' => 'Test']);
        } else { $this->log[] = 'FAIL  permission not created'; }
        $this->hit('role.store', 'POST', '/admin/roles', ['name' => "Role $t", 'slug' => "role-$t", 'permissions' => array_filter([$pe?->id])]);
        $ro = \App\Models\Role::where('slug', "role-$t")->first();
        if ($ro) {
            $this->hit('role.update', 'PUT', "/admin/roles/{$ro->id}", ['name' => "Role2 $t", 'slug' => "role-$t", 'permissions' => []]);
            $this->hit('role.destroy', 'DELETE', "/admin/roles/{$ro->id}");
        } else { $this->log[] = 'FAIL  role not created'; }
        if ($pe) { $this->hit('permission.destroy', 'DELETE', "/admin/permissions/{$pe->id}"); }

        // ── Users
        $this->hit('user.store', 'POST', '/admin/users', ['name' => "User $t", 'email' => "u$t@x.com", 'password' => 'Password1!', 'password_confirmation' => 'Password1!', 'role' => 'customer', 'is_active' => 1]);
        $u = User::where('email', "u$t@x.com")->first();
        if ($u) {
            $this->hit('user.update', 'PUT', "/admin/users/{$u->id}", ['name' => "User2 $t", 'email' => "u$t@x.com", 'role' => 'customer', 'is_active' => 1]);
            $this->hit('user.toggle', 'PATCH', "/admin/users/{$u->id}/toggle-status");
            $this->hit('user.destroy', 'DELETE', "/admin/users/{$u->id}");
        } else { $this->log[] = 'FAIL  user not created'; }
        $this->hit('user.destroy-self (should be blocked)', 'DELETE', "/admin/users/{$this->admin->id}");
        if (! User::whereKey($this->admin->id)->exists()) {
            $this->log[] = 'BUG   admin was able to delete their own account';
        }

        // ── Products
        $sku = "SKU-$t";
        $this->hit('product.store', 'POST', '/admin/products', [
            'name' => "Prod $t", 'category_id' => $cat->id, 'brand_id' => $brand->id, 'sku' => $sku,
            'description' => 'd', 'specifications' => 's', 'base_price' => 100, 'sale_price' => 90, 'is_active' => 1,
            'thumbnail' => $this->img(), 'images' => [$this->img('g1.png')],
            'variants' => [['color_id' => $color->id, 'phone_storage_id' => \App\Models\PhoneStorage::first()->id, 'price' => 100, 'stock' => 5]],
        ]);
        $pr = Product::where('sku', $sku)->first();
        if ($pr) {
            $this->hit('product.show', 'GET', "/admin/products/{$pr->id}");
            $this->hit('product.edit', 'GET', "/admin/products/{$pr->id}/edit");
            $this->hit('product.update', 'PUT', "/admin/products/{$pr->id}", ['name' => "Prod2 $t", 'category_id' => $cat->id, 'brand_id' => $brand->id, 'sku' => $sku, 'base_price' => 120, 'is_active' => 1]);
            $this->hit('product.toggle-status', 'PATCH', "/admin/products/{$pr->id}/toggle-status");
            $this->hit('product.toggle-featured', 'POST', "/admin/products/products/{$pr->id}/toggle-featured");
            $this->hit('product.images.upload', 'POST', "/admin/products/{$pr->id}/images", ['images' => [$this->img('g2.png')]]);
            $this->hit('product.delete-thumbnail', 'DELETE', "/admin/products/{$pr->id}/thumbnail");
            $this->hit('product.destroy', 'DELETE', "/admin/products/{$pr->id}");
        } else { $this->log[] = 'FAIL  product not created'; }

        // ── Stock
        if ($variant) {
            $this->hit('stock.update', 'PUT', "/admin/stock/{$variant->id}", ['stock' => 20]);
            $this->hit('stock.adjust', 'POST', "/admin/stock/{$variant->id}/adjust", ['type' => 'add', 'amount' => 3, 'note' => 't']);
            $this->hit('stock.bulk-update', 'PUT', '/admin/stock/bulk-update', ['stocks' => [$variant->id => ['stock' => 15]]]);
            $this->hit('stock-movement.store', 'POST', '/admin/stock-movements', ['product_variant_id' => $variant->id, 'type' => 'adjustment_in', 'quantity' => 1, 'notes' => 'test']);
        }

        // ── Purchase orders
        if ($supplier && $product) {
            $this->hit('po.store', 'POST', '/admin/purchase-orders', ['supplier_id' => $supplier->id, 'order_date' => now()->toDateString(), 'currency' => 'USD', 'items' => [['product_id' => $product->id, 'product_variant_id' => $variant?->id, 'quantity_ordered' => 2, 'unit_cost' => 10]]]);
            $po = \App\Models\PurchaseOrder::latest('id')->first();
            if ($po) {
                $this->hit('po.show', 'GET', "/admin/purchase-orders/{$po->id}");
                $this->hit('po.edit', 'GET', "/admin/purchase-orders/{$po->id}/edit");
                $this->hit('po.update', 'PUT', "/admin/purchase-orders/{$po->id}", ['supplier_id' => $supplier->id, 'order_date' => now()->toDateString(), 'currency' => 'USD', 'items' => [['product_id' => $product->id, 'quantity_ordered' => 3, 'unit_cost' => 11]]]);
                $this->hit('po.item.store', 'POST', "/admin/purchase-orders/{$po->id}/items", ['product_id' => $product->id, 'quantity_ordered' => 1, 'unit_cost' => 5]);
                $item = $po->items()->latest('id')->first();
                if ($item) {
                    $this->hit('po.item.update', 'PUT', "/admin/purchase-orders/{$po->id}/items/{$item->id}", ['quantity_ordered' => 4]);
                }
                $this->hit('po.submit', 'PATCH', "/admin/purchase-orders/{$po->id}/submit");
                $this->hit('po.receivings.create', 'GET', "/admin/purchase-orders/{$po->id}/receivings/create");
                $po->refresh();
                $items = $po->items->mapWithKeys(fn ($i) => [$i->id => ['purchase_order_item_id' => $i->id, 'quantity_received' => 1, 'quantity' => 1]])->all();
                $this->hit('po.receivings.store', 'POST', "/admin/purchase-orders/{$po->id}/receivings", ['received_date' => now()->toDateString(), 'received_at' => now()->toDateString(), 'items' => array_values($items)]);
                $this->hit('po.cancel', 'PATCH', "/admin/purchase-orders/{$po->id}/cancel");
            }
            $this->hit('po.store-draft', 'POST', '/admin/purchase-orders', ['supplier_id' => $supplier->id, 'order_date' => now()->toDateString(), 'currency' => 'USD', 'status' => 'draft', 'items' => [['product_id' => $product->id, 'quantity_ordered' => 1, 'unit_cost' => 1]]]);
            $po2 = \App\Models\PurchaseOrder::latest('id')->first();
            if ($po2) { $this->hit('po.destroy', 'DELETE', "/admin/purchase-orders/{$po2->id}"); }
        }

        // ── Orders
        $order = Order::where('status', 'pending')->first() ?? Order::first();
        if ($order) {
            $this->hit('order.updateStatus', 'PUT', "/admin/orders/{$order->id}/status", ['status' => 'confirmed']);
            $this->hit('order.export', 'GET', '/admin/orders/export');
            $this->hit('order.cancel', 'POST', "/admin/orders/{$order->id}/cancel");
            $this->hit('order.destroy', 'DELETE', "/admin/orders/{$order->id}");
            $this->hit('invoice.regenerate', 'POST', "/admin/invoices/regenerate/{$order->id}");
        }
        $inv = \App\Models\Invoice::first();
        if ($inv) { $this->hit('invoice.download', 'GET', "/admin/invoices/{$inv->id}/download"); }

        // ── Payments
        $pay = Payment::where('status', 'pending')->first() ?? Payment::first();
        if ($pay) {
            $this->hit('payment.verify', 'PATCH', "/admin/payments/{$pay->id}/verify");
            $this->hit('payment.refund', 'PATCH', "/admin/payments/{$pay->id}/refund");
        }

        // ── Reviews
        $rev = Review::first();
        if ($rev) {
            $this->hit('review.approve', 'PUT', "/admin/reviews/{$rev->id}/approve");
            $this->hit('review.reject', 'PUT', "/admin/reviews/{$rev->id}/reject");
            $this->hit('review.bulkApprove', 'POST', '/admin/reviews/bulk-approve', ['ids' => [$rev->id]]);
            $this->hit('review.destroy', 'DELETE', "/admin/reviews/{$rev->id}");
        }

        // ── Contacts
        $ct = Contact::first();
        if ($ct) {
            $this->hit('contact.reply', 'POST', "/admin/contacts/{$ct->id}/reply", ['reply' => 'Thank you for contacting us!']);
            $this->hit('contact.markRead', 'POST', "/admin/contacts/{$ct->id}/mark-read");
            $this->hit('contact.bulkMarkRead', 'POST', '/admin/contacts/bulk-mark-read', ['ids' => [$ct->id]]);
            $this->hit('contact.destroy', 'DELETE', "/admin/contacts/{$ct->id}");
        }

        // ── Settings
        $this->hit('settings.general', 'PUT', '/admin/settings/general', ['site_name' => 'Ashahi', 'timezone' => 'Asia/Bangkok', 'site_logo' => $this->img()]);
        $this->hit('settings.store', 'PUT', '/admin/settings/store', ['store_email' => 'a@b.com', 'store_phone' => '1', 'store_address' => 'x']);
        $this->hit('settings.payment', 'PUT', '/admin/settings/payment', ['currency' => 'USD', 'payment_gateway' => 'manual']);
        $this->hit('settings.shipping', 'PUT', '/admin/settings/shipping', ['flat_shipping_rate' => 2, 'free_shipping_threshold' => 50]);
        $this->hit('settings.notifications', 'PUT', '/admin/settings/notifications', ['notifications' => []]);
        $this->hit('settings.security', 'PUT', '/admin/settings/security', ['session_lifetime' => 120, 'password_min_length' => 8, 'two_factor_required' => 0]);

        fwrite(STDERR, "\n===== ADMIN CRUD: {$this->ok} OK, ".count($this->log)." problems =====\n".implode("\n", $this->log)."\n");
        $this->assertTrue(true);
    }
}
