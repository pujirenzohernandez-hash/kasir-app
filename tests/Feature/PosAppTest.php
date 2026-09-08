<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosAppTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::create(['name' => 'Snack & Makanan']);
    }

    public function test_dashboard_is_accessible_when_authenticated(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dasbor Utama');
    }

    public function test_category_can_be_created_updated_and_deleted(): void
    {
        $response = $this->actingAs($this->user)->post(route('categories.store'), [
            'name' => 'Minuman Dingin',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Minuman Dingin']);

        $cat = Category::where('name', 'Minuman Dingin')->first();

        $response = $this->actingAs($this->user)->put(route('categories.update', $cat->id), [
            'name' => 'Minuman Segar',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Minuman Segar']);

        $response = $this->actingAs($this->user)->delete(route('categories.destroy', $cat->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['name' => 'Minuman Segar']);
    }

    public function test_product_can_be_created_updated_and_deleted(): void
    {
        $response = $this->actingAs($this->user)->post(route('products.store'), [
            'category_id' => $this->category->id,
            'code'        => 'PRD-TEST-001',
            'name'        => 'Keripik Singkong 100g',
            'price'       => 12000,
            'stock'       => 50,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'code'  => 'PRD-TEST-001',
            'name'  => 'Keripik Singkong 100g',
            'stock' => 50,
        ]);

        $product = Product::where('code', 'PRD-TEST-001')->first();

        $response = $this->actingAs($this->user)->put(route('products.update', $product->id), [
            'category_id' => $this->category->id,
            'code'        => 'PRD-TEST-001',
            'name'        => 'Keripik Singkong Pedas 100g',
            'price'       => 13500,
            'stock'       => 45,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'name'  => 'Keripik Singkong Pedas 100g',
            'price' => 13500,
            'stock' => 45,
        ]);

        $response = $this->actingAs($this->user)->delete(route('products.destroy', $product->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('products', ['code' => 'PRD-TEST-001']);
    }

    public function test_user_can_be_created_updated_and_deleted(): void
    {
        $response = $this->actingAs($this->user)->post(route('users.store'), [
            'name'     => 'Kasir Baru',
            'email'    => 'kasirbaru@test.com',
            'password' => 'password123',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'kasirbaru@test.com']);

        $newUser = User::where('email', 'kasirbaru@test.com')->first();

        $response = $this->actingAs($this->user)->put(route('users.update', $newUser->id), [
            'name'     => 'Kasir Super',
            'email'    => 'kasirsuper@test.com',
            'password' => 'newpassword123',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'kasirsuper@test.com']);

        $response = $this->actingAs($this->user)->delete(route('users.destroy', $newUser->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['email' => 'kasirsuper@test.com']);
    }

    public function test_pos_transaction_flow_and_stock_reduction(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'code'        => 'PRD-POS-01',
            'name'        => 'Teh Pucuk Harum 350ml',
            'price'       => 4000,
            'stock'       => 20,
        ]);

        $payload = [
            'cart' => [
                [
                    'id'       => $product->id,
                    'name'     => $product->name,
                    'price'    => 4000,
                    'qty'      => 3,
                    'subtotal' => 12000,
                ]
            ],
            'pay_amount'     => 20000,
            'payment_method' => 'cash',
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('pos.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status'        => 'success',
            'total_amount'  => 12000,
            'pay_amount'    => 20000,
            'change_amount' => 8000,
        ]);

        $this->assertEquals(17, $product->fresh()->stock);

        $this->assertDatabaseHas('transactions', [
            'total_amount'  => 12000,
            'pay_amount'    => 20000,
            'change_amount' => 8000,
            'user_id'       => $this->user->id,
        ]);

        $this->assertDatabaseHas('transaction_details', [
            'product_id' => $product->id,
            'qty'        => 3,
            'price'      => 4000,
            'subtotal'   => 12000,
        ]);
    }

    public function test_pos_fails_when_payment_is_insufficient(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'code'        => 'PRD-POS-02',
            'name'        => 'Biskuit Roma Kelapa',
            'price'       => 10000,
            'stock'       => 10,
        ]);

        $payload = [
            'cart' => [
                [
                    'id'       => $product->id,
                    'name'     => $product->name,
                    'price'    => 10000,
                    'qty'      => 2,
                    'subtotal' => 20000,
                ]
            ],
            'pay_amount'     => 15000,
            'payment_method' => 'cash',
        ];

        $response = $this->actingAs($this->user)
            ->postJson(route('pos.store'), $payload);

        $response->assertStatus(422);
        $this->assertEquals(10, $product->fresh()->stock);
    }

    public function test_pos_uses_product_price_instead_of_client_price(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'code'        => 'PRD-POS-03',
            'name'        => 'Susu Kotak',
            'price'       => 10000,
            'stock'       => 5,
        ]);

        $response = $this->actingAs($this->user)->postJson(route('pos.store'), [
            'cart' => [[
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => 1,
                'qty'      => 2,
                'subtotal' => 2,
            ]],
            'pay_amount'     => 20000,
            'payment_method' => 'cash',
        ]);

        $response->assertOk()->assertJsonPath('total_amount', 20000);
        $this->assertDatabaseHas('transaction_details', [
            'product_id' => $product->id,
            'price'      => 10000,
            'subtotal'   => 20000,
        ]);
    }

    public function test_pos_combines_duplicate_products_before_checking_stock(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'code'        => 'PRD-POS-04',
            'name'        => 'Kopi Sachet',
            'price'       => 2500,
            'stock'       => 3,
        ]);

        $response = $this->actingAs($this->user)->postJson(route('pos.store'), [
            'cart' => [
                ['id' => $product->id, 'qty' => 2],
                ['id' => $product->id, 'qty' => 2],
            ],
            'pay_amount'     => 10000,
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(422);
        $this->assertEquals(3, $product->fresh()->stock);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_pos_rejects_unknown_payment_method(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'code'        => 'PRD-POS-05',
            'name'        => 'Air Mineral',
            'price'       => 3000,
            'stock'       => 5,
        ]);

        $response = $this->actingAs($this->user)->postJson(route('pos.store'), [
            'cart' => [['id' => $product->id, 'qty' => 1]],
            'pay_amount'     => 3000,
            'payment_method' => 'unknown',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_reports_page_can_be_filtered(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.index', [
            'start_date' => date('Y-m-01'),
            'end_date'   => date('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertSee('Laporan Penjualan');
    }
}


