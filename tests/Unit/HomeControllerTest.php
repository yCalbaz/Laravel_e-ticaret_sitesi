<?php

namespace Tests\Unit;

use App\Http\Controllers\HomeProductController;
use App\Models\Category;
use App\Models\Member;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Foundation\Testing\TestCase;
use Tests\Helpers\DeleteHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class HomeControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    
    public function testProductCreate(): void
    {
        DeleteHelper::delete([
            'products',
            'categories',
            'members',
        ]);
        $member = Member::factory()->create ();
        $category = Category::factory()->create([
            'category_name' => 'Test ayakkabı kategoris',
            'category_slug' => 'test-ayakkabi-kategoris'
        ]);

        $this->actingAs($member);


        $data=[
            'product_name' => 'ayakkabı',
            'product_sku'=>'SKU-123',
            'product_price'=>12,
            'product_image'=>UploadedFile::fake()->image('ayakkabi.png'),
            'category_ids'=>[$category->id],
            'details'=>'deneme ayakkabı',
        ];

        $response = $this->followingRedirects()->post(route('products.store'), $data);

        $response->assertStatus(200);
        $response->assertSee('Ürün başarıyla eklendi.');
    }

    public function testErrorProductPrice()
    {
        DeleteHelper::delete([
            'products',
            'categories',
            'members',
        ]);

        $member= Member::factory()->create();
        $category = Category::factory()->create([
            'category_name' => 'Test ayakkabı kategoris',
            'category_slug' => 'test-ayakkabi-kategoris'
        ]);

        $this->actingAs($member);

        $data=[
            'product_name' => 'ayakkabı',
            'product_sku'=>'SKU-123',
            'product_price'=>-12,
            'product_image'=>UploadedFile::fake()->image('ayakkabi.png'),
            'category_ids'=>[$category->id],
            'details'=>'deneme ayakkabı',
        ];
        
        $response = $this->post(route('products.store'), $data);

        $response->assertSessionHasErrors('product_price');
    }

    public function testWithoutLogin()
    {
        DeleteHelper::delete([
            'products',
            'categories',
            'members',
        ]);
        $category= Category::factory()->create([
            'category_name' => 'Test ayakkabı kategoris',
            'category_slug' => 'test-ayakkabi-kategoris'
        ]);

        $data=[
            'product_name' => 'ayakkabı',
            'product_sku'=>'SKU-123',
            'product_price'=>12,
            'product_image'=> UploadedFile::fake()->image('ayakkabi.png'),
            'category_ids'=>[$category->id],
            'details'=>'deneme ayakkabı',
        ];
        $response= $this->post(route('products.store'), $data);
        $response->assertSessionHasErrors('error');
    }
    public function testErrorProductPriceZero()
    {
        DeleteHelper::delete([
            'products',
            'categories',
            'members',
        ]);

        $member = Member::factory()->create();
        $category = Category::factory()->create([
            'category_name' => 'Test ayakkabı kategoris',
            'category_slug' => 'test-ayakkabi-kategoris'
        ]);

        $this->actingAs($member);

        $data = [
            'product_name' => 'ayakkabı',
            'product_sku' => 'SKU-129',
            'product_price' => 0, 
            'product_image' => UploadedFile::fake()->image('ayakkabi.png'),
            'category_ids' => [$category->id],
            'details' => 'deneme ayakkabı',
        ];

        $response = $this->post(route('products.store'), $data);
        $response->assertSessionHasErrors('product_price');
    }

    public function testIndexGetCategory()
    {
        DeleteHelper::delete(['categories']);
        $member = Member::factory()->create();
        $this->actingAs($member);

        $category = Category::factory()->create([
            'category_name'=> 'test kategori',
            'category_slug'=> 'test-kategori'
        ]);

        $response=$this->get(route('product.index.form'));
        $response->assertStatus(200);
        $response->assertViewIs('product_panel');
        $response->assertViewHas('categories');
        $this->assertTrue($response->viewData('categories')->contains($category));

    }
    public function testAuthIndexGetCategory()
    {
        DeleteHelper::delete(['categories']);

        $category = Category::factory()->create([
            'category_name'=> 'test kategori',
            'category_slug'=> 'test-kategori'
        ]);

        $response=$this->get(route('product.index.form'));
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));

    }

    public function testProductHome()
{
    DeleteHelper::delete([
        'products',
        'stores'
    ]);
    Http::fake([
        'http://host.docker.internal:3000/stock/*' => Http::response(['stores' => [['stock' => 10]]], 200),
    ]);

    $store =Store::factory()->create([
        'id' => 1, 
    ]);
    
    $product = Product::factory()->create(); 
    $product->stocks()->create([
        'size_id' => 1, 
        'stock' => 10,  
        'store_id' => 1,
    ]);
    $response = $this->get(route('home.product')); 

    $response->assertStatus(200);
    $this->assertCount(1, $response->viewData('products')); 
}
}
