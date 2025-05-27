<?php

namespace Tests\Unit;

use App\Models\Member;
use App\Models\MemberStore;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Tests\Helpers\DeleteHelper;
use Tests\TestCase;

class StockControllerTest extends TestCase
{
    private function memberPut()
    {
        $member = Member::factory()->create();
        Session::put('customer_id',$member->customer_id);
        $this->actingAs($member ,'web');
        return $member;
    }
    /**
     * store
     */
    
    public function testStockStoreNotAuthhority()
    {
        DeleteHelper::delete([
            'stocks',
            'stores',
            'members',
            'products',
            'sizes',
            'member_store'
        ]);
        $member = $this->memberPut();
        $store = Store::factory()->create();
        $product = Product::factory()->create();

        $response = $this->post(route('stock.store'), [
            'store_id' => $store->id,
            'product_sku' => $product->product_sku,
            'sizes' => [
                1 => 10,
            ],
            'size_ids' => [1],
        ]);

        $response->assertJson([
            'error' => 'Bu depoya stok ekleme yetkiniz yok.'
        ]);
    }

    public function testStockStoreNot()
    {
        DeleteHelper::delete([
            'stocks',
            'stores',
            'members',
            'products',
            'sizes',
            'member_store'
        ]);
        $member = $this->memberPut();
        $product = Product::factory()->create();

        $response = $this->post(route('stock.store'), [
            'store_id' => 5,
            'product_sku' => $product->product_sku,
            'sizes' => [
                1 => 10,
            ],
            'size_ids' => [1],
        ]);

        $response->assertJson([
            'error' => 'Geçerli depo girin.'
        ]);
    }

    public function testStoreNotSizeError()
    {
        DeleteHelper::delete([
            'stocks',
            'stores',
            'members',
            'products',
            'sizes',
            'member_store'
        ]);
        $member = $this->memberPut();
        $store = Store::factory()->create();
        $product = Product::factory()->create();

        MemberStore::create([
            'member_id' => $member->id,
            'store_id' => $store->id,
        ]);
        $response = $this->post(route('stock.store'), [
            'store_id' => $store->id,
            'product_sku' => $product->product_sku,
        ]);

        $response->assertSessionHasErrors(['sizes']);
        $this->assertDatabaseMissing('stocks', [
            'store_id' => $store->id,
            'product_sku' => $product->product_sku,
        ]);
    }

    public function testStoreNotSizeId()
    {
        DeleteHelper::delete([
            'stocks',
            'stores',
            'members',
            'products',
            'sizes',
            'member_store'
        ]);
        $member = $this->memberPut();
        $store = Store::factory()->create();
        $product = Product::factory()->create();

        MemberStore::create([
            'member_id' => $member->id,
            'store_id' => $store->id,
        ]);
        $response = $this->post(route('stock.store'), [
            'store_id' => $store->id,
            'product_sku' => $product->product_sku,
            'sizes' => [
                1 => 10,
                2 => 5,
            ],
        ]);

        $response->assertSessionHasErrors(['size_ids']); 
        $this->assertDatabaseMissing('stocks', [
            'store_id' => $store->id,
            'product_sku' => $product->product_sku,
            'size_id' => 1,
        ]);
        $this->assertDatabaseMissing('stocks', [
            'store_id' => $store->id,
            'product_sku' => $product->product_sku,
            'size_id' => 2,
        ]);
    }
}
