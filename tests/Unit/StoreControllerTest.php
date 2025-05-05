<?php

namespace Tests\Unit;

use App\Models\Member;
use App\Models\Store;
use Illuminate\Support\Facades\Session;
use Tests\Helpers\DeleteHelper;
use Tests\TestCase;

class StoreControllerTest extends TestCase
{
    private function memberPut()
    {
        $member = Member::factory()->create();
        Session::put('customer_id',$member->customer_id);
        $this->actingAs($member ,'web');
        return $member;
    }
    /**
     * index
     */
    public function testIndexGetView()
    {
        DeleteHelper::delete([
            'members',
            'stores'
        ]);
        $member = $this->memberPut();

        $response = $this->get(route('store.index.form'));
        $response->assertStatus(200);
        $response->assertViewIs('store_panel');
    }

    /**
     * store
     */

     public function testStoreSuccess()
     {
        DeleteHelper::delete([
            'members',
            'stores'
        ]);
        $member = $this->memberPut();
        $data = [
            'store_name' => 'Test Depo',
            'store_max' => 100,
            'store_priority' => 1,
        ];

        $response = $this->post(route('store.store'), $data);
        $response->assertStatus(200); 
        $response->assertJson([
            'success' => 'Depo başarıyla eklendi ve yetkisi verildi :)'
        ]);

        $this->assertDatabaseHas('stores', ['store_name' => 'Test Depo']);
        $this->assertDatabaseHas('member_store', [
            'member_id' => $member->id,
            'store_id' => Store::first()->id,
        ]);
     }

     public function testStoreValidationError()
     {
        $member = $this->memberPut();

        $response = $this->post(route('store.store'), [
            'store_name' => '',         
            'store_max' => 'sayı olacak test',   
            'store_priority' => 0,      
        ]);
        $response->assertStatus(302); 
        $response->assertSessionHasErrors([
            'store_name' => 'Depo ismi boş geçilemez', 
            'store_max' => 'Max satış adedi sayı olmalıdır', 
            'store_priority' => 'Depo önceliği geçerli sayı olmalıdır',
        ]);
     }

     public function testStoreNotMember()
     {
        DeleteHelper::delete([
            'members', 
            'stores'
        ]);
        
        $data = [
            'store_name' => 'Yetkisiz Depo',
            'store_max' => 30,
            'store_priority' => 5,
        ];
    
        $response = $this->post(route('store.store'), $data);
    
        $response->assertStatus(401); 
        $response->assertJson([
            'error' => 'Oturum bilgisi bulunamadı. Depo yetkisi atanamadı.'
        ]);
        
        $this->assertDatabaseHas('stores', ['store_name' => 'Yetkisiz Depo']);
     }
}
 