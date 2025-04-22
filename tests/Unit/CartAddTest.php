<?php

namespace Tests\Unit;

use App\Http\Controllers\BasketController;
use App\Models\Basket;
use App\Models\BasketItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;

class CartAddTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    /** @test */
    protected function mockHttpResponse(array $body = [], int $status = 200)
        {
            $response = $this->mock(\Illuminate\Http\Client\Response::class);
            $response->shouldReceive('failed')->once()->andReturn($status >= 400);
            $response->shouldReceive('json')->once()->andReturn($body);
            $response->shouldReceive('getStatusCode')->once()->andReturn($status);
            return $response;
        }
    /** @test */
    public function add_to_cart_is_true()
    {
        $request=$this->mock(Request::class);
        $request->shouldReceive('validate')->once()->andReturnSelf();
        $request->shouldReceive('input')->with('quantity')->andReturn(2);
        $request->shouldReceive('input')->with('size_id')->andReturn(1);

        $product = new Product(['id' => 1, 'product_sku' => 'SKU12345', 'product_name' => 'Test Ürünü', 'product_price' => 10.00, 'product_image' => 'image.jpg']);
        $productMock = $this->mock(Product::class);
        $productMock->shouldReceive('where')->once()->with('product_sku', 'SKU-123')->andReturnSelf();
        $productMock->shouldReceive('first')->once()->andReturn($product);

        Http::shouldReceive('get')->once()->with('http://host.docker.internal:3000/stock/SKU12345/1')->andReturn(
            $this->mockHttpResponse(['stores' => [['stock' => 10]]])
        );

        Session::shouldReceive('get')->once()->with('customer_id')->andReturn(12345);

        $basketMock = $this->mock(Basket::class);
        $basketMock->shouldReceive('firstOrCreate')->once()->with(['customer_id' => 123, 'is_active' => 1])->andReturn(new Basket(['id' => 1]));

        $basketItemMock = $this->mock(BasketItem::class);
        $basketItemMock->shouldReceive('where')->once()->with('order_id', 1)->andReturnSelf();
        $basketItemMock->shouldReceive('where')->once()->with('product_sku', 'SKU-123')->andReturnSelf();
        $basketItemMock->shouldReceive('where')->once()->with('size_id', 1)->andReturnSelf();
        $basketItemMock->shouldReceive('first')->once()->andReturn(null); 
        $basketItemMock->shouldReceive('create')->once()->with([
            'product_name' => 'Test Ürünü',
            'product_sku' => 'SKU-123',
            'product_piece' => 2,
            'product_price' => 10.00,
            'product_image' => 'image.jpg',
            'order_id' => 1,
            'size_id' => 1,
        ]);

        $basketItemMock->shouldReceive('where')->once()->with('order_id', 1)->andReturnSelf();
        $basketItemMock->shouldReceive('sum')->once()->with('product_piece')->andReturn(2);

        $this->app->instance(Request::class, $request);
        $this->app->instance(Product::class, $productMock);
        $this->app->instance(Basket::class, $basketMock);
        $this->app->instance(BasketItem::class, $basketItemMock);

        
        $controller = new BasketController();
        $response = $controller->add($request, 'SKU-123');

       
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertStringContainsString('Ürün sepete eklendi!', $response->getContent());
        $this->assertStringContainsString('"cartCount":2', $response->getContent());
    
    }
}
