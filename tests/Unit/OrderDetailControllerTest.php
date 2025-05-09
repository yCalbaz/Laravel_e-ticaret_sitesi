<?php

namespace Tests\Unit;

use App\Models\Basket;
use App\Models\Member;
use App\Models\OrderBatch;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\Size;
use App\Models\Store;
use Illuminate\Support\Facades\Session;
use Tests\Helpers\DeleteHelper;
use App\Models\OrderCanceled;
use Tests\TestCase;
        
    class OrderDetailControllerTest extends TestCase
    {
        private function memberPut(){
            $member = Member::factory()->create();
            Session::put('customer_id',$member->customer_id);
            $this->actingAs($member ,'web');
            return $member;
        }
        /* showReturn */
        
        public function testShowReturnExpired()
        {
            DeleteHelper::delete([
                'order_lines',
                'order_batches',
                'stores',
                'products',
                'sizes',
                'members'
            ]);
            $member = $this->memberPut();
            $store = Store::factory()->create(); 
            $order = OrderBatch::factory()->create([
                'created_at' => now()->subDays(10),
                'customer_id' =>$member->id,
                ]);
            $response = $this->get(route('order.returnForm', [
                'orderId' => $order->id,
                'store_id'=> $store->id
            ]));
            $response->assertStatus(200);
            $response->assertJson([
                'success' => 'İptal formu başarıyla alındı.',
            ]);
        }

        public function testShowReturnNoOrderId()
        {
            $this->memberPut();
            $store = Store::factory()->create(); 
            $response = $this->get(route('order.returnForm',[
                'orderId'=>999,
                'store_id'=>$store->id
            ]));
            $response->assertStatus(404);
            $response->assertJson([
                'error' => 'Sipariş bulunamadı.'
            ]);
        }
        

        public function testShowReturnNotExpired()
        {
            DeleteHelper::delete([
                'order_lines',
                'order_batches',
                'stores',
                'products',
                'sizes',
                'members'
            ]);
        
            $member = $this->memberPut();
            $store = Store::factory()->create(); 
            $order = OrderBatch::factory()->create([
                'created_at' => now()->subDays(20),
                'customer_id' => $member->id,
                ]);
            $response = $this->get(route('order.returnForm', [
                'orderId' => $order->id,
                'store_id'=> $store->id
            ]));
            $response->assertStatus(400);
            $response->assertJson([
                'error' => 'Bu sipariş için iade süresi dolmuştur.'
            ]);
        }

        /* processReturn */

        public function testProcessReturnValidationSucces()
        {
            DeleteHelper::delete([
                'order_canceled',
                'products',
                'members',
                'order_batches',
                'order_lines',
                'stores'
            ]);
            
            $member = $this->memberPut();
            $product = Product::factory()->create([]);
            $store = Store::factory()->create();
            $orderBatch = OrderBatch::create([
                'customer_id'=>$member->id,
                'customer_name'=>$member->name,
                'customer_address'=>'istanbul,çekmeköy, mehmet akif,florya,12,türkiye'
            ]);
            OrderLine::create([
                'order_id'=>$orderBatch->id,
                'order_batch_id'=>$orderBatch->id,
                'product_sku'=>$product->product_sku,
                'product_name'=>$product->product_name,
                'store_id'=>$store->id,
                'product_piece'=>1,
                'updated_at'=> '2025-04-30 06:32:11', 
                'created_at'=>'2025-04-30 06:32:11' 
            ]);

            OrderCanceled::create([
                'order_id'=> $orderBatch->id,
                'product_sku'=>$product->product_sku,
                'product_image'=>$product->product_image,
                'customer_id'=>$member->customer_id,
            ]);
            $response = $this->post(route('order.processReturn'),[
                'details'=>'',
                'return_address'=>'başarısız adres girişi'
            ],['Accept' => 'application/json']);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([
            'details',
            'return_address',
            'product_sku'
            ]);
        }

        public function testProcessReturnNotFoundOrders()
        {
            DeleteHelper::delete([
                'order_canceled',
                'products',
                'members',
                'order_batches',
                'order_lines',
                'stores'
            ]);
            $member = $this->memberPut();
            OrderBatch::factory()->create([
                'customer_id'=> $member->id,
                'customer_name'=> 'test isim',
                'customer_address'=>'istanbul,çekmeköy, mehmet akif,florya,12,türkiye',
                'product_price'=>100,
                'order_id'=>2
            ]);
            $response = $this->post(route('order.processReturn'),[
                'details'=>'deneme detayı',
                'return_address'=>'istanbul,çekmeköy, mehmet akif,florya,12,türkiye',
                'product_sku'=> ['SKU-123'],
                'order_id'=>2
            ],['Accept' => 'application/json']);

            $response->assertSessionHas('error', 'Sipariş bulunamadı.');
        }

        public function testProcessReturnMoreThanFifteenDays()
        {
            $this->memberPut();

            $order = OrderBatch::factory()->create(['created_at' => now()->subDays(16)]);

            $returnDetails = [
                'order_id' => $order->id,
                'details' => 'deneme test',
                'return_address' => 'istanbul, çekmeköy, mehmet akif, florya, 12, Türkiye',
                'product_sku' => ['SKU-123'],
                'store_id' => 1,
            ];

            
            $request = $this->post(route('order.processReturn'), $returnDetails);

            
            $request->assertStatus(400);
            $request->assertJson(['error' => 'Bu sipariş için iade süresi dolmuştur.']);
        }

        public function testProcessReturnSuccess()
        {
            DeleteHelper::delete([
                'products',
                'members',
                'order_batches',
                'order_lines',
                'stores',
                'order_canceled'
            ]);
            $member = $this->memberPut();

            $order = OrderBatch::factory()->create(['customer_id' => $member->id, 
                'customer_name' => $member->name,
                'customer_address' => 'istanbul,çekmeköy, mehmet akif,florya,12,türkiye',]);
            $store = Store::factory()->create(['id' => 1]);

            $product = Product::factory()->create();
            
          
            $orderLine = OrderLine::create([
                'order_batch_id' => $order->id, 
                'product_id' => $product->id, 
                'store_id' => $store->id,
                'product_sku'=>$product->product_sku,
                'product_name'=>$product->product_name,
                'order_id'=>$order->id, 
                'order_id' => $order->id,
                'product_piece' => 1,
                "order_status" => 'sipariş alındı'

            ]);
            
            
            $returnDetails = [
                'order_id' => $order->id,
                'details' => 'Ürün beklediğim gibi değil.',
                'return_address' => 'istanbul,çekmeköy, mehmet akif,florya,12,türkiye',
                'product_sku' => [$product->product_sku],
                'store_id' => $store->id,
            ];
            OrderCanceled::create([
                'order_id' => $order->id,
                'details' => 'Ürün beklediğim gibi değil.',
                'customer_id' => $member->customer_id,
                'return_address' => 'istanbul,çekmeköy, mehmet akif,florya,12,türkiye',
                'product_price' => $orderLine->product_price,
                'product_sku' => implode(',', [$product->product_sku]),
            ]);
            $request = $this->post(route('order.processReturn'), $returnDetails);

            
            $this->assertDatabaseHas('order_canceled', [
                'order_id' => $order->id,
                'details' => 'Ürün beklediğim gibi değil.',
                'customer_id' => $member->customer_id,
                'return_address' => 'istanbul,çekmeköy, mehmet akif,florya,12,türkiye',
                'product_price' => $orderLine->product_price,
                'product_sku' => implode(',', [$product->product_sku]),
            ]);

            $this->assertDatabaseHas('order_lines', [
                'id' => $orderLine->id,
                'order_status' =>  'iptal talebi alındı',
            ]);
            

            $request->assertStatus(200);
            $request->assertJson(['success' => 'İade talebiniz alındı.']);
        }
        
    }
 
         
    
 