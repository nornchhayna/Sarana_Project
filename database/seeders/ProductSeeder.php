<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\PhoneModel;
use App\Models\PhoneRam;
use App\Models\PhoneStorage;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductStorage;
use App\Models\ProductVariant;
use App\Models\Stock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Cache lookups
        $categories = Category::pluck('id', 'slug')->toArray();
        $brands = Brand::pluck('id', 'slug')->toArray();
        $models = PhoneModel::pluck('id', 'slug')->toArray();
        $colors = Color::pluck('id', 'name')->toArray();
        $storages = PhoneStorage::pluck('id', 'capacity')->toArray();
        $rams = PhoneRam::pluck('id', 'capacity')->toArray();

        // Helper to safely get ID or fallback to first
        $getCatId = fn($slug) => $categories[$slug] ?? reset($categories);
        $getBrandId = fn($slug) => $brands[$slug] ?? reset($brands);
        $getModelId = fn($slug) => $models[$slug] ?? null;

        $productsData = [
            [
                'name' => 'iPhone 16 Pro Max',
                'slug' => 'iphone-16-pro-max',
                'sku' => 'IP-16-PM-001',
                'category_slug' => 'phone-cases',
                'brand_slug' => 'apple',
                'model_slug' => 'iphone-16-pro-max',
                'description' => "Experience Apple's most advanced smartphone with a stunning Super Retina XDR OLED 120Hz display, aerospace-grade titanium frame, A18 Pro chip, 48MP fusion camera with 5x telephoto optical zoom, and outstanding battery life.",
                'specifications' => json_encode([
                    'Screen' => '6.9-inch Super Retina XDR OLED (120Hz)',
                    'Chip' => 'Apple A18 Pro chip (3nm)',
                    'Camera' => '48MP Main + 48MP Ultra-Wide + 12MP 5x Telephoto',
                    'Battery' => 'Up to 33 hours video playback',
                    'Material' => 'Grade 5 Titanium',
                ]),
                'details' => json_encode([
                    'In the Box' => 'iPhone 16 Pro Max, USB-C Charge Cable (1m), Documentation',
                    'Warranty' => '1 Year Official Apple Warranty',
                    'Connectivity' => '5G, Wi-Fi 7, Bluetooth 5.3, MagSafe',
                ]),
                'base_price' => 1199.00,
                'sale_price' => 1099.00,
                'discount' => 8.34,
                'thumbnail' => 'products/wZyKPZBun0zVyWBOQYzYRha4ovkiTXSdZDERDb3D.png',
                'images' => [
                    'products/EkQqNI1KRR8Q5lCut6Y66UNBhpM0or5Knh4pTibk.webp',
                    'products/zkXsTZrZMKIDLfgXonPTIVuQPEIzFtcEviQ3zoDc.png',
                    'products/W9dHCnlBZyE9Fhcw1gjYjU6tpIfeQCtHCcP3fOjf.png',
                ],
                'is_active' => true,
                'is_featured' => true,
                'is_new' => true,
                'is_flash_sale' => true,
                'total_sold' => 48,
                'avg_rating' => 4.95,
                'color_names' => ['Natural Titanium', 'Black Titanium', 'Desert Titanium'],
                'storage_capacities' => ['256GB', '512GB', '1TB'],
                'ram_capacities' => ['8GB'],
            ],
            [
                'name' => 'Samsung Galaxy S25 Ultra',
                'slug' => 'samsung-galaxy-s25-ultra',
                'sku' => 'SM-S25-UT-001',
                'category_slug' => 'phone-cases',
                'brand_slug' => 'samsung',
                'model_slug' => 'galaxy-s25-ultra',
                'description' => "Premium Android smartphone featuring an immersive Dynamic AMOLED 2X display, Qualcomm Snapdragon 8 Elite processor, Galaxy AI integration, built-in S Pen, and 200MP Quad Telephoto camera system.",
                'specifications' => json_encode([
                    'Screen' => '6.8-inch Dynamic AMOLED 2X, 120Hz, HDR10+',
                    'Processor' => 'Snapdragon 8 Elite for Galaxy',
                    'Camera' => '200MP + 50MP Periscope + 50MP Ultra-Wide + 10MP Telephoto',
                    'Battery' => '5,000mAh with 45W Super Fast Charging',
                    'Stylus' => 'Integrated Bluetooth S Pen',
                ]),
                'details' => json_encode([
                    'In the Box' => 'Galaxy S25 Ultra, S Pen, Type-C Cable, SIM Eject Tool',
                    'Warranty' => '1 Year Samsung Official Warranty',
                ]),
                'base_price' => 1299.00,
                'sale_price' => 1199.00,
                'discount' => 7.70,
                'thumbnail' => 'products/NKcCcb35VyUz0NAOGy4h1PbQurzDyZhjJWOHcOA5.png',
                'images' => [
                    'products/9Z21L6hI8qy74UV2xQeFyGw2J4hCIn40A5mhebdJ.png',
                    'products/obSeEYt22dQUyN8VtJPRAKAHpbeWHWg4JdD7IvSj.png',
                ],
                'is_active' => true,
                'is_featured' => true,
                'is_new' => true,
                'is_flash_sale' => false,
                'total_sold' => 36,
                'avg_rating' => 4.88,
                'color_names' => ['Black Titanium', 'Silver', 'Titanium Yellow'],
                'storage_capacities' => ['256GB', '512GB', '1TB'],
                'ram_capacities' => ['12GB', '16GB'],
            ],
            [
                'name' => 'Xiaomi 15 Ultra',
                'slug' => 'xiaomi-15-ultra',
                'sku' => 'XM-15-UT-001',
                'category_slug' => 'phone-cases',
                'brand_slug' => 'xiaomi',
                'model_slug' => 'xiaomi-15-ultra',
                'description' => "Pro photography flagship co-engineered with Leica. Equipped with a 1-inch variable aperture sensor, 200MP periscope telephoto lens, Snapdragon 8 Elite chipset, and stunning 2K OLED display.",
                'specifications' => json_encode([
                    'Screen' => '6.73-inch 2K LTPO AMOLED, 120Hz, Dolby Vision',
                    'Processor' => 'Snapdragon 8 Elite (3nm)',
                    'Camera' => 'Leica Quad Camera with 1-inch 50MP Sensor + 200MP Telephoto',
                    'Battery' => '5,300mAh with 90W HyperCharge, 80W Wireless',
                ]),
                'details' => json_encode([
                    'In the Box' => 'Xiaomi 15 Ultra, 90W Fast Charger, USB Cable, Protective Case',
                    'Warranty' => '1 Year Official Warranty',
                ]),
                'base_price' => 999.00,
                'sale_price' => 899.00,
                'discount' => 10.01,
                'thumbnail' => 'products/MGazXtkzd2efgG1iUvAJOvMzmBM8ODKPNucaJzxM.png',
                'images' => [
                    'products/5seBMfx0zqgOTjY4pMcHKS2vOhLY6aejZTT5b4l9.png',
                ],
                'is_active' => true,
                'is_featured' => true,
                'is_new' => true,
                'is_flash_sale' => true,
                'total_sold' => 22,
                'avg_rating' => 4.80,
                'color_names' => ['Black', 'Silver'],
                'storage_capacities' => ['256GB', '512GB'],
                'ram_capacities' => ['12GB', '16GB'],
            ],
            [
                'name' => 'OPPO Find X8 Pro',
                'slug' => 'oppo-find-x8-pro',
                'sku' => 'OP-X8-PRO-001',
                'category_slug' => 'phone-cases',
                'brand_slug' => 'oppo',
                'model_slug' => 'find-x8-pro',
                'description' => "Hasselblad Master Camera system, dual periscope telephoto lenses, ultra-narrow quad-curved display, Dimensity 9400 processor, and a massive 5910mAh glacier battery.",
                'specifications' => json_encode([
                    'Screen' => '6.78-inch AMOLED, 1.5K, 120Hz adaptive',
                    'Processor' => 'MediaTek Dimensity 9400',
                    'Camera' => 'Hasselblad Dual Periscope 50MP Quad Camera',
                    'Battery' => '5,910mAh Glacier Battery with 80W SuperVOOC',
                ]),
                'details' => json_encode([
                    'In the Box' => 'OPPO Find X8 Pro, 80W SuperVOOC Charger, Type-C Cable, Case',
                ]),
                'base_price' => 949.00,
                'sale_price' => 869.00,
                'discount' => 8.43,
                'thumbnail' => 'products/CHvj273t1J8E5Ja3Nhx3wawWkkna0hWRWgIGeV3I.png',
                'images' => [
                    'products/fHgKEiU4pDiVhsuvtnAzACJ53Wj5sEKHgXP4oKix.webp',
                ],
                'is_active' => true,
                'is_featured' => true,
                'is_new' => false,
                'is_flash_sale' => false,
                'total_sold' => 19,
                'avg_rating' => 4.75,
                'color_names' => ['Black', 'Deep Blue', 'White'],
                'storage_capacities' => ['256GB', '512GB'],
                'ram_capacities' => ['12GB', '16GB'],
            ],
            [
                'name' => 'Apple AirPods Pro (2nd Generation)',
                'slug' => 'apple-airpods-pro-2nd-gen',
                'sku' => 'APP-2ND-GEN',
                'category_slug' => 'earphones',
                'brand_slug' => 'apple',
                'model_slug' => null,
                'description' => "Featuring up to 2x more Active Noise Cancellation, Adaptive Audio, Transparency mode, and Personalized Spatial Audio with dynamic head tracking for truly immersive sound.",
                'specifications' => json_encode([
                    'Audio Tech' => 'Active Noise Cancellation, Adaptive Audio, Transparency mode',
                    'Chip' => 'Apple H2 headphone chip, Apple U1 chip in case',
                    'Battery' => 'Up to 6 hours listening time, 30 hours with MagSafe Case (USB-C)',
                    'Resistance' => 'Dust, sweat, and water resistant (IP54)',
                ]),
                'details' => json_encode([
                    'In the Box' => 'AirPods Pro, MagSafe Case (USB-C), Silicone ear tips (4 sizes), Cable',
                    'Warranty' => '1 Year Apple Warranty',
                ]),
                'base_price' => 249.00,
                'sale_price' => 229.00,
                'discount' => 8.03,
                'thumbnail' => 'products/malefbfndSwudtNdId9mhOrhGwQGNo9Qeiin0iDG.png',
                'images' => [
                    'products/Uw8D6WO72noc4WWEyS3cTLwZK1iKYabEcg2nsQ1z.png',
                ],
                'is_active' => true,
                'is_featured' => true,
                'is_new' => true,
                'is_flash_sale' => true,
                'total_sold' => 85,
                'avg_rating' => 4.92,
                'color_names' => ['White'],
                'storage_capacities' => [],
                'ram_capacities' => [],
            ],
            [
                'name' => 'ESR MagSafe Armor Clear Case',
                'slug' => 'esr-magsafe-armor-clear-case',
                'sku' => 'ESR-MAG-ARMOR',
                'category_slug' => 'phone-cases',
                'brand_slug' => 'esr',
                'model_slug' => 'iphone-16-pro-max',
                'description' => "Military-grade drop protection with powerful built-in magnets for seamless MagSafe accessories. Anti-yellowing crystal clear back shows off your phone's original color.",
                'specifications' => json_encode([
                    'Material' => 'Shock-absorbing TPU bumper + Scratch-resistant PC back',
                    'Drop Protection' => 'Military Grade MIL-STD-810H',
                    'Magnet Strength' => '1,500g holding force (MagSafe compatible)',
                ]),
                'details' => json_encode([
                    'Compatibility' => 'iPhone 16 Pro Max (6.9 inch)',
                    'Features' => 'Raised screen edges & camera guard bezel',
                ]),
                'base_price' => 29.99,
                'sale_price' => 21.99,
                'discount' => 26.68,
                'thumbnail' => 'products/V5capvwUeboK02XVrrl1Rlg1jAm36SWLGVyC9S7R.png',
                'images' => [
                    'products/MBD9mcxlXE5dIceLgwVX9vnjbl77nzWyALUtUHJ0.webp',
                ],
                'is_active' => true,
                'is_featured' => true,
                'is_new' => false,
                'is_flash_sale' => false,
                'total_sold' => 110,
                'avg_rating' => 4.85,
                'color_names' => ['White', 'Black'],
                'storage_capacities' => [],
                'ram_capacities' => [],
            ],
            [
                'name' => 'ESR 9H Tempered Glass Screen Protector (2-Pack)',
                'slug' => 'esr-9h-tempered-glass-screen-protector',
                'sku' => 'ESR-9H-GLASS-2P',
                'category_slug' => 'screen-protectors',
                'brand_slug' => 'esr',
                'model_slug' => 'iphone-16-pro-max',
                'description' => "Ultra-tough 9H hardness tempered glass with auto-alignment installation tray. Oleophobic coating protects against fingerprints and oil smudges.",
                'specifications' => json_encode([
                    'Hardness' => '9H Triple-Hardened Tempered Glass',
                    'Thickness' => '0.33mm ultra-thin',
                    'Clarity' => '99.9% HD Transparency',
                ]),
                'details' => json_encode([
                    'Package Includes' => '2x Glass Protectors, 1x Easy Installation Frame, Cleaning Kits',
                ]),
                'base_price' => 18.00,
                'sale_price' => 13.50,
                'discount' => 25.00,
                'thumbnail' => 'products/OHauIXlofWeUsOTG2Kb0S6jfsAlDa3fjCZbuSFXC.webp',
                'images' => [
                    'products/OHauIXlofWeUsOTG2Kb0S6jfsAlDa3fjCZbuSFXC.webp',
                ],
                'is_active' => true,
                'is_featured' => false,
                'is_new' => false,
                'is_flash_sale' => false,
                'total_sold' => 140,
                'avg_rating' => 4.90,
                'color_names' => ['Black'],
                'storage_capacities' => [],
                'ram_capacities' => [],
            ],
            [
                'name' => 'Anker 65W GaN Fast Wall Charger (3-Port)',
                'slug' => 'anker-65w-gan-fast-wall-charger',
                'sku' => 'ANK-GAN-65W',
                'category_slug' => 'chargers',
                'brand_slug' => 'anker',
                'model_slug' => null,
                'description' => "Powered by GaNPrime technology. High-speed charging for up to 3 devices simultaneously with 2 USB-C ports and 1 USB-A port in an ultra-compact form factor.",
                'specifications' => json_encode([
                    'Total Output' => '65W Max',
                    'Ports' => '2x USB-C, 1x USB-A',
                    'Technology' => 'GaNPrime, ActiveShield 2.0 temperature monitoring',
                    'Compatibility' => 'MacBook, iPhone, Samsung Galaxy, iPad, and more',
                ]),
                'details' => json_encode([
                    'In the Box' => 'Anker 65W Charger, Silicone cable tie, Welcome Guide',
                    'Warranty' => '24 Months Anker Warranty',
                ]),
                'base_price' => 49.99,
                'sale_price' => 39.99,
                'discount' => 20.00,
                'thumbnail' => 'products/eq0jYFdnvJqISznDYN61KpjFCypKjF6DE8hsGiJK.png',
                'images' => [
                    'products/WFEQK8HgH1sLGgylBQv3V79CGt4sWmN26fPGnAfL.png',
                ],
                'is_active' => true,
                'is_featured' => true,
                'is_new' => true,
                'is_flash_sale' => true,
                'total_sold' => 74,
                'avg_rating' => 4.94,
                'color_names' => ['Black', 'White'],
                'storage_capacities' => [],
                'ram_capacities' => [],
            ],
            [
                'name' => 'Baseus 15W Magnetic Wireless Fast Charging Pad',
                'slug' => 'baseus-15w-magnetic-wireless-charger',
                'sku' => 'BAS-MAG-15W',
                'category_slug' => 'wireless-chargers',
                'brand_slug' => 'baseus',
                'model_slug' => null,
                'description' => "Ultra-slim aluminum alloy magnetic wireless charger with 15W peak output. Compatible with MagSafe iPhones, Qi-enabled Androids, and AirPods.",
                'specifications' => json_encode([
                    'Output Power' => '15W / 10W / 7.5W / 5W',
                    'Material' => 'Aluminum Alloy + Tempered Glass surface',
                    'Cable Length' => '1.5m integrated USB-C braided cable',
                ]),
                'details' => json_encode([
                    'Safety Protections' => 'Overheat, over-voltage, foreign object detection',
                ]),
                'base_price' => 28.00,
                'sale_price' => 21.00,
                'discount' => 25.00,
                'thumbnail' => 'products/2MEqdMe40zX5gGJ7XPNk7Ias4abtL1cPGW8f8YDh.png',
                'images' => [
                    'products/2MEqdMe40zX5gGJ7XPNk7Ias4abtL1cPGW8f8YDh.png',
                ],
                'is_active' => true,
                'is_featured' => true,
                'is_new' => false,
                'is_flash_sale' => false,
                'total_sold' => 52,
                'avg_rating' => 4.70,
                'color_names' => ['Black', 'Silver'],
                'storage_capacities' => [],
                'ram_capacities' => [],
            ],
            [
                'name' => 'Smart Watch GT Pro Active Health Edition',
                'slug' => 'smart-watch-gt-pro-active-edition',
                'sku' => 'SW-GT-PRO-01',
                'category_slug' => 'smart-watches',
                'brand_slug' => 'samsung',
                'model_slug' => null,
                'description' => "Sapphire glass AMOLED display, titanium chassis, comprehensive 24/7 heart-rate, SpO2 and ECG tracking, dual-frequency GPS, and up to 14 days battery life.",
                'specifications' => json_encode([
                    'Display' => '1.43-inch Sapphire AMOLED (466x466)',
                    'Water Resistance' => '5ATM + IP68 Water & Dust Resistant',
                    'Battery' => 'Up to 14 days on typical usage, wireless fast charging',
                    'Sensors' => 'Heart Rate, SpO2, Skin Temp, Barometer, Compass, ECG',
                ]),
                'details' => json_encode([
                    'In the Box' => 'Smart Watch GT Pro, Fluoroelastomer Strap, Magnetic Cradle',
                    'Warranty' => '1 Year Manufacturer Warranty',
                ]),
                'base_price' => 299.00,
                'sale_price' => 249.00,
                'discount' => 16.72,
                'thumbnail' => 'products/IcOJfKEo092dm4L9qzEb9E4nVmUZpoIOX39LbnQC.png',
                'images' => [
                    'products/IcOJfKEo092dm4L9qzEb9E4nVmUZpoIOX39LbnQC.png',
                ],
                'is_active' => true,
                'is_featured' => true,
                'is_new' => true,
                'is_flash_sale' => true,
                'total_sold' => 31,
                'avg_rating' => 4.82,
                'color_names' => ['Black Titanium', 'Silver'],
                'storage_capacities' => [],
                'ram_capacities' => [],
            ],
            [
                'name' => 'ESR Individual Camera Lens Protector (3-Pack)',
                'slug' => 'esr-individual-camera-lens-protector',
                'sku' => 'ESR-CAM-LENS-3P',
                'category_slug' => 'camera-protectors',
                'brand_slug' => 'esr',
                'model_slug' => 'iphone-16-pro-max',
                'description' => "Aviation aluminum alloy metal rings with 9H tempered glass covers. High optical transparency preserves flash performance and original photo resolution.",
                'specifications' => json_encode([
                    'Material' => 'Aerospace-Grade Aluminum Ring + 9H Sapphire Coating Glass',
                    'Feature' => 'Night circle ring prevents flash glare in dark photos',
                ]),
                'details' => json_encode([
                    'In the Box' => '3x Lens Protector Rings, 1x Removal Tool, Cleaning Wipe',
                ]),
                'base_price' => 16.00,
                'sale_price' => 12.00,
                'discount' => 25.00,
                'thumbnail' => 'products/OTcnePOnWfUad31Zj3aFF8kiBqqovR57XFXt1qcx.webp',
                'images' => [
                    'products/OTcnePOnWfUad31Zj3aFF8kiBqqovR57XFXt1qcx.webp',
                ],
                'is_active' => true,
                'is_featured' => false,
                'is_new' => false,
                'is_flash_sale' => false,
                'total_sold' => 88,
                'avg_rating' => 4.79,
                'color_names' => ['Natural Titanium', 'Black Titanium', 'Desert Titanium'],
                'storage_capacities' => [],
                'ram_capacities' => [],
            ],
            [
                'name' => 'Ugreen USB-C to USB-C 100W Braided Cable (2m)',
                'slug' => 'ugreen-usb-c-to-usb-c-100w-braided-cable',
                'sku' => 'UG-USBC-100W',
                'category_slug' => 'charging-cables',
                'brand_slug' => 'baseus',
                'model_slug' => null,
                'description' => "Heavy-duty nylon braided 100W Power Delivery cable with E-marker chip. Supports 5A/20V rapid charging for laptops, tablets, and flagship smartphones.",
                'specifications' => json_encode([
                    'Maximum Power' => '100W (20V/5A) Power Delivery 3.0',
                    'Data Transfer' => '480 Mbps USB 2.0 speed',
                    'Durability' => 'Tested over 15,000 bend cycles',
                ]),
                'details' => json_encode([
                    'Length' => '2 Meters / 6.6 Feet',
                    'Jacket' => 'Double-braided high-density nylon',
                ]),
                'base_price' => 15.00,
                'sale_price' => 11.50,
                'discount' => 23.33,
                'thumbnail' => 'products/WFEQK8HgH1sLGgylBQv3V79CGt4sWmN26fPGnAfL.png',
                'images' => [
                    'products/WFEQK8HgH1sLGgylBQv3V79CGt4sWmN26fPGnAfL.png',
                ],
                'is_active' => true,
                'is_featured' => false,
                'is_new' => false,
                'is_flash_sale' => false,
                'total_sold' => 125,
                'avg_rating' => 4.86,
                'color_names' => ['Black'],
                'storage_capacities' => [],
                'ram_capacities' => [],
            ],
            [
                'name' => 'Anker PowerCore 20,000mAh 30W Power Bank',
                'slug' => 'anker-powercore-20000mah-30w-power-bank',
                'sku' => 'ANK-PC-20K-30W',
                'category_slug' => 'power-banks',
                'brand_slug' => 'anker',
                'model_slug' => null,
                'description' => "High-capacity 20,000mAh portable charger with 30W USB-C high-speed charging. Can charge an iPhone 16 up to 4.5 times or a Galaxy S25 up to 3.8 times.",
                'specifications' => json_encode([
                    'Capacity' => '20,000mAh / 74Wh',
                    'Output' => '1x USB-C 30W Max, 1x USB-A 22.5W Max',
                    'Recharge Time' => 'Approx 3.5 hours with 30W wall charger',
                ]),
                'details' => json_encode([
                    'Airline Approved' => 'Yes (under 100Wh)',
                    'Safety' => 'MultiProtect 11-point safety system',
                ]),
                'base_price' => 59.99,
                'sale_price' => 47.99,
                'discount' => 20.00,
                'thumbnail' => 'products/eq0jYFdnvJqISznDYN61KpjFCypKjF6DE8hsGiJK.png',
                'images' => [
                    'products/eq0jYFdnvJqISznDYN61KpjFCypKjF6DE8hsGiJK.png',
                ],
                'is_active' => true,
                'is_featured' => true,
                'is_new' => false,
                'is_flash_sale' => true,
                'total_sold' => 65,
                'avg_rating' => 4.91,
                'color_names' => ['Black'],
                'storage_capacities' => [],
                'ram_capacities' => [],
            ],
        ];

        foreach ($productsData as $data) {
            $catId = $getCatId($data['category_slug']);
            $brandId = $getBrandId($data['brand_slug']);
            $modelId = !empty($data['model_slug']) ? $getModelId($data['model_slug']) : null;

            $product = Product::withTrashed()->updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'sku' => $data['sku'],
                    'description' => $data['description'],
                    'specifications' => $data['specifications'],
                    'details' => $data['details'],
                    'category_id' => $catId,
                    'brand_id' => $brandId,
                    'phone_model_id' => $modelId,
                    'base_price' => $data['base_price'],
                    'sale_price' => $data['sale_price'],
                    'discount' => $data['discount'],
                    'thumbnail' => $data['thumbnail'],
                    'images' => $data['images'],
                    'is_active' => $data['is_active'],
                    'is_featured' => $data['is_featured'],
                    'is_new' => $data['is_new'],
                    'is_flash_sale' => $data['is_flash_sale'],
                    'status' => 'active',
                    'total_sold' => $data['total_sold'],
                    'avg_rating' => $data['avg_rating'],
                    'deleted_at' => null,
                ]
            );

            // Sync Product Colors
            $colorIds = [];
            foreach ($data['color_names'] as $colorName) {
                if (isset($colors[$colorName])) {
                    $cId = $colors[$colorName];
                    $colorIds[] = $cId;
                    ProductColor::updateOrCreate(
                        ['product_id' => $product->id, 'color_id' => $cId],
                        ['price_adjustment' => 0.00]
                    );

                    // Add to product_images table if image exists
                    ProductImage::updateOrCreate(
                        ['product_id' => $product->id, 'color_id' => $cId],
                        [
                            'image' => $data['thumbnail'],
                            'is_primary' => true,
                            'sort_order' => 0,
                        ]
                    );
                }
            }

            // Sync Product Storages
            $storageIds = [];
            foreach ($data['storage_capacities'] as $cap) {
                if (isset($storages[$cap])) {
                    $sId = $storages[$cap];
                    $storageIds[] = $sId;
                    $priceAdj = ($cap === '512GB') ? 100.00 : (($cap === '1TB') ? 250.00 : 0.00);
                    ProductStorage::updateOrCreate(
                        ['product_id' => $product->id, 'phone_storage_id' => $sId],
                        ['price_adjustment' => $priceAdj]
                    );
                }
            }

            // Generate Variants & Stock
            if (!empty($colorIds) && !empty($storageIds)) {
                $ramId = !empty($data['ram_capacities']) && isset($rams[$data['ram_capacities'][0]])
                    ? $rams[$data['ram_capacities'][0]]
                    : null;

                foreach ($colorIds as $cId) {
                    foreach ($storageIds as $sId) {
                        $skuVar = $product->sku . "-C{$cId}-S{$sId}";
                        $stockQty = rand(15, 45);

                        ProductVariant::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'color_id' => $cId,
                                'phone_storage_id' => $sId,
                            ],
                            [
                                'phone_ram_id' => $ramId,
                                'sku' => $skuVar,
                                'price' => $product->current_price,
                                'stock' => $stockQty,
                                'is_active' => true,
                            ]
                        );

                        Stock::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'color_id' => $cId,
                                'phone_storage_id' => $sId,
                            ],
                            [
                                'quantity' => $stockQty,
                                'low_stock_threshold' => 5,
                            ]
                        );
                    }
                }
            } elseif (!empty($colorIds)) {
                // For accessories with colors but without storage sizes
                foreach ($colorIds as $cId) {
                    $stockQty = rand(20, 60);

                    Stock::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'color_id' => $cId,
                            'phone_storage_id' => null,
                        ],
                        [
                            'quantity' => $stockQty,
                            'low_stock_threshold' => 10,
                        ]
                    );
                }
            } else {
                // Single stock entry
                Stock::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'color_id' => null,
                        'phone_storage_id' => null,
                    ],
                    [
                        'quantity' => 50,
                        'low_stock_threshold' => 10,
                    ]
                );
            }
        }
    }
}
