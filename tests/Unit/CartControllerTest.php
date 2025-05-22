<?php

namespace Tests\Unit;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Member;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Support\Facades\Session;
use Tests\Helpers\DeleteHelper;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use App\Models\ModelLog;

class CartControllerTest extends TestCase
{

    private function memberControl()
    {
        $member = Member::factory()->create();
        Session::put('customer_id',$member->customer_id);
        $this->actingAs($member);
        return $member;
    }
    // Api'leri config tablosunda tut burada kullanacağın yerde kullan.
    /**
     * İndex FOnksiyonu
     */
    public function testIndexViewReturn()
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'sizes',
            'members',
            'products',
            
        ]);

        $member = $this->memberControl();
        $product = Product::factory()->create([
            'product_sku' => 'TEST-SKU',
            'product_price' => 100,
            'discount_rate' => 10,
        ]);
        $size = Size::factory()->create(['size_name' => '42']);

        $basket = Basket::create([
            'customer_id' => $member->customer_id,
            'is_active' => 1
        ]);

        BasketItem::create([
            'product_name' => $product->product_name,
            'product_sku' => $product->product_sku,
            'product_piece' => 2,
            'product_price' => $product->product_price,
            'product_image' => 'image.png',
            'order_id' => $basket->id,
            'size_id' => $size->id,
        ]);

        Session::put('customer_id', $member->customer_id);

        $response = $this->actingAs($member)->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cart');
        $response->assertViewHasAll(['cartItems', 'totalPrice', 'cargoTotalPrice']);
        
        $viewData = $response->viewData('cartItems');

        $this->assertNotEmpty($viewData);
        $this->assertEquals(2, $viewData[0]->product_piece);
        $this->assertEquals('42', $viewData[0]->size_name);
        $this->assertEquals(90, $viewData[0]->discounted_price); 
    }

    public function testIndexNotFoundCustomer()
    {
        Session::flush(); 
        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cart');
        $response->assertViewHas('cartItems', []);
        $response->assertViewHas('sepetSayisi', 0);
    }

    public function testIndexNotFoundBasket()
    {
        DeleteHelper::delete(['members']);
        $member = $this->memberControl();
        $response = $this->actingAs($member)->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cart');
        $response->assertViewHas('cartItems', []);
        $response->assertViewHas('sepetSayisi', 0);
    }

    /**
     * Add FOnksiyonu
     */

    public function testAddProductSuccess()
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'sizes',
            'members',
            'products',
        ]);
        $member = $this->memberControl();
        $product= Product::factory()->create(['product_sku'=>'SKU-1324', 'product_price'=>100]);
        $size= Size::factory()->create();


        Http::fake(["http://host.docker.internal:3000/stock/{$product->product_sku}/{$size->id}"
                        =>Http::response([
                            'stores' => [
                                ['store_id' => 1, 'stock' => 3],
                                ['store_id' => 2, 'stock' => 2]
                            ]
                        ], 200)
                    ]);
        $response = $this->actingAs($member)->post(route('cart.add', $product->product_sku), [
            'quantity' => 2,
            'size_id' => $size->id,
            '_token' => csrf_token()
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => 'Ürün sepete eklendi!',
            'cartCount' => 2]);
        $this->assertDatabaseHas(
            'basket_items', [
            'product_sku' => $product->product_sku,
            'product_piece' => 2,
            'size_id' => $size->id
            ]);

    }

    public function testAddProductNotStocks()
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'sizes',
            'members',
            'products',
            'logs',
        ]);

        $member = $this->memberControl();

        $product = Product::factory()->create([
            'product_sku'=>'SKU-123',
            'product_price'=>19
        ]);
        $size = Size::factory()->create();
        Http::fake(["http://host.docker.internal:3000/stock/{$product->product_sku}/{$size->id}"
        =>Http::response([
            'stores'=>[
                ['store_id'=> 1 ,'stocks'=>1],
                ]
            ], 200)
        ]);

        $response= $this->actingAs($member)->post(route('cart.add', $product->product_sku),
        [
            'quantity'=>3,
            'size_id'=>$size->id,
            '_token'=>csrf_token()
        ]);
        $response->assertStatus(400);
        $response->assertJson(['error'=>'Yeterli stok bulunmamaktadır.']);
        $this->assertDatabaseMissing('basket_items',
        [
            'product_sku'=>$product->product_sku,
        ]);
        $this->assertDatabaseHas('logs', [
            'operaton' => 'add_to_cart',
            'error' => 'Yeterli stok yok',
            'success' => 'Hata', 
        ]);
    }


    public function testAddProductNotService()
    {
        
            DeleteHelper::delete([
                'basket_items',
                'baskets',
                'sizes',
                'members',
                'products',
                'logs',
            ]);
            $member = $this->memberControl();
            $product = Product::factory()->create(['product_sku' => 'SKU-1234']);
            $size = Size::factory()->create();
            Http::fake([
                "http://host.docker.internal:3000/stock/{$product->product_sku}/{$size->id}" => Http::response('Service Unavailable', 503),
            ]);
    
            $response = $this->actingAs($member)->post(route('cart.add', $product->product_sku), [
                'quantity' => 1,
                'size_id' => $size->id,
                '_token' => csrf_token(),
            ]);
    
            $response->assertStatus(500);
            $response->assertJson(['error' => 'Servise ulaşılamadı']);
            $this->assertDatabaseMissing('basket_items', ['product_sku' => $product->product_sku]);
            $this->assertDatabaseHas('logs', [
                'operaton' => 'add_to_cart',
                'error' => 'Servise ulaşılamadı',
            ]);
    }

    public function testAddProductResponseFalse()
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'sizes',
            'members',
            'products',
            'logs'
        ]);
        $member = $this->memberControl();
        $product = Product::factory()->create(['product_sku' => 'SKU-1234']);
        $size = Size::factory()->create();
        Http::fake([
            "http://host.docker.internal:3000/stock/{$product->product_sku}/{$size->id}" => Http::response(['not_stores' => []], 200),
        ]);

        $response = $this->actingAs($member)->post(route('cart.add', $product->product_sku), [
            'quantity' => 1,
            'size_id' => $size->id,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Servis yanıtı geçersiz']);
        $this->assertDatabaseMissing('basket_items', ['product_sku' => $product->product_sku]);
        $this->assertDatabaseHas('logs', [
            'operaton' => 'add_to_cart',
            'error' => 'Geçersiz servis yanıtı',
        ]);
    
    }

    public function testAddProducMultiplyHaveStock()
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'sizes',
            'members',
            'products',
        ]);
        $member = $this->memberControl();
        $product = Product::factory()->create(['product_sku' => 'SKU-1234', 'product_price' => 10, ]);
        $size = Size::factory()->create(['size_name' => 'M']);
        $basket = Basket::create(['customer_id' => $member->customer_id, 'is_active' => 1]);

        BasketItem::create([
            'order_id' => $basket->id,
            'product_sku' => $product->product_sku,
            'product_name' => $product->product_name,
            'product_price' => $product->product_price,
            'product_image' => 'old_image.png',
            'product_piece' => 1,
            'size_id' => $size->id,
        ]);

        Http::fake([
            "http://host.docker.internal:3000/stock/{$product->product_sku}/{$size->id}" => Http::response(['stores' => [['store_id' => 1, 'stock' => 5]]], 200),
        ]);

        $response = $this->actingAs($member)->post(route('cart.add', $product->product_sku), [
            'quantity' => 2,
            'size_id' => $size->id,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => 'Ürün sepete eklendi!', 'cartCount' => 3]);
        $this->assertDatabaseHas('basket_items', [
            'order_id' => $basket->id,
            'product_sku' => $product->product_sku,
            'size_id' => $size->id,
            'product_piece' => 3, 
        ]);
    }

    public function testAddInsufficientStock()
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'sizes',
            'members',
            'products',
        ]);
        $member = $this->memberControl();
        $product = Product::factory()->create(['product_sku' => 'SKU-1234', 'product_price' => 75]);
        $size = Size::factory()->create(['size_name' => 'L']);
        $basket = Basket::create(['customer_id' => $member->customer_id, 'is_active' => 1]);
        BasketItem::create([
            'order_id' => $basket->id,
            'product_sku' => $product->product_sku,
            'product_name' => $product->product_name,
            'product_price' => $product->product_price,
            'product_image' => 'another_image.png',
            'product_piece' => 2,
            'size_id' => $size->id,
        ]);

        Http::fake([
            "http://host.docker.internal:3000/stock/{$product->product_sku}/{$size->id}" => Http::response(['stores' => [['store_id' => 2, 'stock' => 1]]], 200),
        ]);

        $response = $this->actingAs($member)->post(route('cart.add', $product->product_sku), [
            'quantity' => 2, 
            'size_id' => $size->id,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Yeterli stok bulunmamaktadır.']);
        $this->assertDatabaseHas('basket_items', [
            'order_id' => $basket->id,
            'product_sku' => $product->product_sku,
            'size_id' => $size->id,
            'product_piece' => 2, 
        ]);
    }

    public function testAddCreateCartForCustomer()
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'sizes',
            'products',
        ]);
        $product = Product::factory()->create(['product_sku' => '123', 'product_price' => 250]);
        $size = Size::factory()->create();
        Http::fake([
            "http://host.docker.internal:3000/stock/{$product->product_sku}/{$size->id}" => Http::response(['stores' => [['store_id' => 1, 'stock' => 3]]], 200),
        ]);

        $response = $this->post(route('cart.add', $product->product_sku), [
            'quantity' => 1,
            'size_id' => $size->id,
            '_token' => csrf_token(),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => 'Ürün sepete eklendi!', 'cartCount' => 1]);
        $this->assertDatabaseHas('baskets', ['is_active' => 1]);
        $basket = Basket::where('is_active', 1)->first();
        $this->assertNotNull($basket->customer_id);

        $this->assertEquals($basket->customer_id, Session::get('customer_id'));

        $this->assertDatabaseHas('basket_items', [
            'order_id' => $basket->id,
            'product_sku' => $product->product_sku,
            'size_id' => $size->id,
            'product_piece' => 1,
        ]);
    }
   
  
/* Delete fonksiyonu */

    public function testDeleteTrue()
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'sizes',
            'products',
            'members',
            
        ]);

        $member = $this->memberControl();
        $product = Product::factory()->create(['product_sku' => 'SKU-123', 'product_price' => 75]);
        $size = Size::factory()->create(['size_name' => 'L']);
        $basket = Basket::create(['customer_id' => $member->customer_id, 'is_active' => 1]);
        $basketItem= BasketItem::create([
            'order_id' => $basket->id,
            'product_sku' => $product->product_sku,
            'product_name' => $product->product_name,
            'product_price' => $product->product_price,
            'product_image' => 'test_img.png',
            'product_piece' => 2,
            'size_id' => $size->id,
        ]);

        $response =$this-> actingAs($member)->delete(route('cart.delete',$basketItem->id),[
            'order_id' => $basket->id,
            'product_sku' => $product->product_sku,
            'product_name' => $product->product_name,
            'product_price' => $product->product_price,
            'product_image' => 'test_img.png',
            'quantity' => 1, 
            'size_id' => $size->id,
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Ürün sepetten kaldırıldı!']);
    }

/* Approvl */

    public function testApprovlNotFoundCart()
    {
        
        $member = $this->memberControl();
    
        $response = $this->actingAs($member)->get(route('sepet.approvl'));
        
        $response->assertStatus(404); 
        $response->assertJson(['error' => 'Sepet bulunamadı.']);
    }

    public function testApprovlQuantityOneLittle()
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'sizes',
            'members',
            'products',
            'order_lines',
            'order_batches'
        ]);
        $member = $this->memberControl();
        $size = Size::factory()->create();
        $product = Product::factory()->create(['product_sku' => 'SKU-1234']);
        $basket = Basket::create(['customer_id' => $member->customer_id, 'is_active' => 1]);

        BasketItem::create([
            'order_id' => $basket->id,
            'product_sku' => $product->product_sku,
            'product_name' => $product->product_name,
            'product_price' => $product->product_price,
            'product_image' => 'test.jpg',
            'product_piece' => 0,
            'size_id' => $size->id,
        ]); 
        $requestData = [
            'name' => 'Test İsim',
            'email'=>'test@gmail.com',
            'address' => 'Test adres, Test adres, Test adres, Test adres, 12345, Test adres',
            'cardNumber' => '1234567890123456',
            'expiryDate' => '12/24',
            'cvv' => '123',
            'cardHolderName' => 'Test İsim',
        ];
        Http::fake([
            "http://host.docker.internal:3000/stock/{$product->product_sku}/{$size->id}" => Http::response(['stores' => [['store_id' => 1, 'stock' => 3, 'store_max'=>25, 'store_priority' => 1]]], 200),
        ]);
        $response = $this->actingAs($member)->post(route('sepet.approvl'), [
            'quantity' => 0,
            'size_id' => $size->id,
            '_token' => csrf_token(),
        ]+ $requestData);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Sepette geçersiz ürün adedi var!']);
        
    
    }

    public function testApprovlNotService()
    {
         DeleteHelper::delete([
            'order_lines',
            'order_batches',
            'baskets',
            'basket_items',
            'members',
            'products',
            'sizes',
        ]);
        $member = $this->memberControl();
        $size = Size::factory()->create();
        $product = Product::factory()->create(['product_sku' => 'SKU-1234']);
        $basket = Basket::create(['customer_id' => $member->customer_id, 'is_active' => 1]);
        
        Http::fake([
            "http://host.docker.internal:3000/stock/{$product->product_sku}/{$size->id}" => Http::response([], 503),
        ]);
        BasketItem::create([
            'order_id' => $basket->id,
            'product_sku' => $product->product_sku,
            'product_name' => $product->product_name,
            'product_price' => $product->product_price,
            'product_image' => 'test.jpg',
            'product_piece' => 1,
            'size_id' => $size->id,
        ]); 
        $requestData = [
            'name' => 'Test İsim',
            'email'=>'test@gmail.com',
            'address' => 'Test adres, Test adres, Test adres, Test adres, 12345, Test adres',
            'cardNumber' => '1234567890123456',
            'expiryDate' => '12/24',
            'cvv' => '123',
            'cardHolderName' => 'Test İsim',
        ];
        $response = $this->actingAs($member)->post(route('sepet.approvl'),$requestData);

        $response->assertStatus(503);
        $response->assertJson(['error'=> 'Stok servis bağlantısında bir hata oluştu.']);
        
    
    }

    public function testApprovlNotStock()
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'sizes',
            'members',
            'products',
            'order_lines',
            'order_batches'
        ]);
        $member = $this->memberControl();
        $size = Size::factory()->create();
        $product = Product::factory()->create(['product_sku' => 'SKU-1234']);
        $basket = Basket::create(['customer_id' => $member->customer_id, 'is_active' => 1]);

        BasketItem::create([
            'order_id' => $basket->id,
            'product_sku' => $product->product_sku,
            'product_name' => $product->product_name,
            'product_price' => $product->product_price,
            'product_image' => 'test.jpg',
            'product_piece' => 4,
            'size_id' => $size->id,
        ]); 
        $requestData = [
            'name' => 'Test İsim',
            'email'=>'test@gmail.com',
            'address' => 'Test adres, Test adres, Test adres, Test adres, 12345, Test adres',
            'cardNumber' => '1234567890123456',
            'expiryDate' => '12/24',
            'cvv' => '123',
            'cardHolderName' => 'Test İsim',
        ];
        Http::fake([
            "http://host.docker.internal:3000/stock/{$product->product_sku}/{$size->id}" => Http::response(['stores' => [['store_id' => 1, 'stock' => 3, 'store_max'=>25, 'store_priority' => 1]]], 200),
        ]);

        $response = $this->actingAs($member)->post(route('sepet.approvl'), [
            'quantity' => 4,
            'size_id' => $size->id,
            '_token' => csrf_token(),
        ]+ $requestData);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Yeterli stok yok!']);
        
    }
    public function testApprovlValidation()
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'sizes',
            'members',
            'products',
            'order_lines',
            'order_batches'
        ]);
        $member = $this->memberControl();
        $size = Size::factory()->create();
        $product = Product::factory()->create(['product_sku' => 'SKU-1234']);
        $basket = Basket::create(['customer_id' => $member->customer_id, 'is_active' => 1]);
        BasketItem::create([
            'order_id' => $basket->id,
            'product_sku' => $product->product_sku,
            'product_name' => $product->product_name,
            'product_price' => $product->product_price,
            'product_image' => 'test.jpg',
            'product_piece' => 4,
            'size_id' => $size->id,
        ]); 

        $response = $this->post(route('sepet.approvl'), [
            
            'name' => '', 
            'address' => '', 
            'cardNumber' => '123456', 
            'expiryDate' => '13/30', 
            'cvv' => '12', 
            'cardHolderName' => '', 
        ],['Accept' => 'application/json']);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'name',
            'address',
            'cardNumber',
            'expiryDate',
            'cvv',
            'cardHolderName'
        ]);
    }
    
 /* Update */

    public function testUpdateTrue()
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'members',
            'sizes',
            'products'
        ]);

        $member = $this->memberControl();

        $basket = Basket::create([
            'customer_id'=>$member->customer_id,
            'is_active'=>1
        ]);

        $size = Size::factory()->create();
        $basketItem = BasketItem::create([
            'order_id' =>$basket->id,
            'product_sku' => 'SKU-123',
            'product_name' => 'test ürün',
            'product_price' => 100,
            'product_image' => 'test.jpg',
            'product_piece' => 4,
            'size_id' => $size->id,
        ]);

        $newQuantity=5;

        $response =$this->put(route('cart.update',$basketItem->id),[
            'adet'=>$newQuantity
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => 'Sepet güncellendi.', 'totalPrice' => 100 *$newQuantity]);

        $this->assertDatabaseHas('basket_items', [
            'id' => $basketItem->id,
            'product_piece' => $newQuantity,
        ]);

    }

    public function testUpdateFalse()
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'members',
            'sizes',
            'products'
        ]);

        $notBasket = 99999;
        $response =$this->put(route('cart.update',$notBasket),[
            'adet'=>2
        ]);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Ürün bulunamadı.']);
    }
}


    

