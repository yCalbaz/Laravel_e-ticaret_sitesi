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

class CartControllerTest extends TestCase
{
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

        $member = Member::factory()->create(['customer_id' => 12345678]);
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
        $member = Member::factory()->create(['customer_id' => 999]);
        Session::put('customer_id', $member->customer_id);
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
        $member= Member::factory()->create(['customer_id'=>1111222]);
        $product= Product::factory()->create(['product_sku'=>'SKU-1324', 'product_price'=>100]);
        $size= Size::factory()->create();

        Session::put('customer_id', $member->customer_id);

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
        ]);

        $member= Member::factory()->create([
            'customer_id'=>1123
        ]);

        $product = Product::factory()->create([
            'product_sku'=>'SKU-123',
            'product_price'=>19
        ]);
        $size = Size::factory()->create();

        Session::put('customer_id',$member->customer_id);
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
    }

    public function testAddProductNotFound()
    {
        DeleteHelper::delete(
            [
                'basket_items',
                'baskets',
                'sizes',
                'members',
                'products'
            ]);

        $member = Member::factory()->create(
            [
                'customer_id'=>1234
            ]);
        $size = Size::factory()->create();
        $product_sku = 'SKU-1234'; 
        Session::put('customer_id', $member->customer_id);
        Http::fake(["http://host.docker.internal:3000/stock/{$product_sku}/{$size->id}"
        =>Http::response([],200)
        ]);

        $response= $this->actingAs($member)->post(route('cart.add', $product_sku),
        [
            'quantity'=>3,
            'size_id'=>$size->id,
            '_token'=>csrf_token()
        ]);
        $response->assertStatus(404);
        $response->assertJson(['error'=>'Ürün bulunamadı']);

    }

    
    public function testAddProductTokenError()//hatalı bu kontrol et
    {
        DeleteHelper::delete([
            'basket_items',
            'baskets',
            'sizes',
            'members',
            'products',
        ]);
        $member= Member::factory()->create(['customer_id'=>1111222]);
        $product= Product::factory()->create(['product_sku'=>'SKU-1324', 'product_price'=>100]);
        $size= Size::factory()->create();

        Session::put('customer_id',$member->customer_id);
        $invalidToken= 'invalid_token';

        
        $response = $this->actingAs($member)->post(route('cart.add', $product->product_sku), [
            'quantity' => 2,
            'size_id' => $size->id,
           '_token' => $invalidToken
        ]);
            
        //$response->assertStatus(500);
        $response->assertStatus(419);
        $response->assertJson([
            'error' => 'CSRF token uyuşmazlığı. Lütfen sayfayı yenileyin ve tekrar deneyin.'
        ]);
    }

}
    

