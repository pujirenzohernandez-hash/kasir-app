<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Default
        $admin = User::firstOrCreate(
            ['email' => 'admin@kasir.com'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );
        // Pastikan role admin sudah ter-update jika record sudah ada
        $admin->update(['role' => 'admin']);

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@kasir.com'],
            [
                'name'     => 'Kasir Toko',
                'password' => Hash::make('password'),
                'role'     => 'kasir',
            ]
        );
        // Pastikan role kasir sudah ter-update jika record sudah ada
        $kasir->update(['role' => 'kasir']);

        // 2. Kategori Produk
        $categoriesData = [
            'Makanan & Snack',
            'Minuman',
            'Sembako & Bumbu',
            'Perawatan Diri',
            'Kebutuhan Rumah',
        ];

        $categories = [];
        foreach ($categoriesData as $catName) {
            $categories[$catName] = Category::firstOrCreate(['name' => $catName]);
        }

        // 3. Produk Contoh
        $productsData = [
            [
                'category' => 'Makanan & Snack',
                'code'     => 'SNK-001',
                'name'     => 'Chitato Sapi Panggang 68g',
                'price'    => 11500,
                'stock'    => 35,
            ],
            [
                'category' => 'Makanan & Snack',
                'code'     => 'SNK-002',
                'name'     => 'Oreo Vanilla 133g',
                'price'    => 9500,
                'stock'    => 40,
            ],
            [
                'category' => 'Makanan & Snack',
                'code'     => 'SNK-003',
                'name'     => 'SilverQueen Cashew 58g',
                'price'    => 17000,
                'stock'    => 20,
            ],
            [
                'category' => 'Minuman',
                'code'     => 'MNM-001',
                'name'     => 'Aqua Botol 600ml',
                'price'    => 4000,
                'stock'    => 50,
            ],
            [
                'category' => 'Minuman',
                'code'     => 'MNM-002',
                'name'     => 'Teh Botol Sosro Kotak 250ml',
                'price'    => 4500,
                'stock'    => 45,
            ],
            [
                'category' => 'Minuman',
                'code'     => 'MNM-003',
                'name'     => 'Nescafe Coffee Can 220ml',
                'price'    => 10500,
                'stock'    => 25,
            ],
            [
                'category' => 'Sembako & Bumbu',
                'code'     => 'SMB-001',
                'name'     => 'Beras Premium Ramos 5kg',
                'price'    => 75000,
                'stock'    => 15,
            ],
            [
                'category' => 'Sembako & Bumbu',
                'code'     => 'SMB-002',
                'name'     => 'Minyak Goreng Bimoli 2L',
                'price'    => 36000,
                'stock'    => 18,
            ],
            [
                'category' => 'Sembako & Bumbu',
                'code'     => 'SMB-003',
                'name'     => 'Indomie Goreng Original',
                'price'    => 3500,
                'stock'    => 100,
            ],
            [
                'category' => 'Perawatan Diri',
                'code'     => 'PRW-001',
                'name'     => 'Sabun Lifebuoy Total 10 85g',
                'price'    => 5000,
                'stock'    => 30,
            ],
            [
                'category' => 'Perawatan Diri',
                'code'     => 'PRW-002',
                'name'     => 'Shampo Clear Men Anti Dandruff 160ml',
                'price'    => 26000,
                'stock'    => 12,
            ],
            [
                'category' => 'Kebutuhan Rumah',
                'code'     => 'KBT-001',
                'name'     => 'Deterjen Rinso Molto Liquid 750ml',
                'price'    => 21000,
                'stock'    => 14,
            ],
            [
                'category' => 'Kebutuhan Rumah',
                'code'     => 'KBT-002',
                'name'     => 'Sunlight Pencuci Piring Jeruk Nipis 700ml',
                'price'    => 14500,
                'stock'    => 4, // low stock test
            ],
        ];

        foreach ($productsData as $prod) {
            Product::firstOrCreate(
                ['code' => $prod['code']],
                [
                    'category_id' => $categories[$prod['category']]->id,
                    'name'        => $prod['name'],
                    'price'       => $prod['price'],
                    'stock'       => $prod['stock'],
                ]
            );
        }
    }
}

