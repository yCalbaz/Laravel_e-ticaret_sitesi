<?php

namespace Tests\Unit;
        
use App\Models\Member;
use App\Models\OrderBatch;
use App\Models\OrderLine;
use App\Models\Product;
use App\Models\Size;
use App\Models\Store;
use Illuminate\Support\Facades\Session;
use Tests\Helpers\DeleteHelper;
use Illuminate\Foundation\Testing\TestCase;
        
    class OrderDetailControllerTest extends TestCase
    {
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
        
            $member = Member::factory()->create();
            Session::put('customer_id',$member->customer_id);
            $this->actingAs($member);
            $store = Store::factory()->create(); 
            $order = OrderBatch::factory()->create([
                'created_at' => now()->subDays(10),
                'customer_id' => $member->id,
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
            $member = Member::factory()->create();
            Session::put('customer_id', $member->customer_id);
            $this->actingAs($member);

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
        
            $member = Member::factory()->create();
            Session::put('customer_id',$member->customer_id);
            $this->actingAs($member);
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

        


        
    }
         
    
 