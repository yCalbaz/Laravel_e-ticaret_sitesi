<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UrlTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_product_page(): void
    {
        $response = $this->get('/urun');

        $response->assertStatus(200);
    }
}
