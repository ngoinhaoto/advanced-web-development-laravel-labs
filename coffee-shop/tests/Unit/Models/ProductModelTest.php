<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    public function test_able_to_get_category(): void
    {
        $product = Product::factory()->create();
        $this->assertInstanceOf(Category::class, $product->category);
    }

    public function test_able_to_get_formatted_price()
    {
        $product = Product::factory()->create();
        $expected_results = number_format($product->price) . ' ' . $product->currency;
        $this->assertSame($expected_results, $product->getFormattedPriceAttribute());
    }

    public function test_able_to_get_formatted_total_amount()
    {
        $quantity = rand(3, 10);
        $product = Product::factory()->create();
        $expected_results = number_format($product->price * $quantity) . ' ' . $product->currency;
        $this->assertSame($expected_results, $product->getFormattedTotalAmount($quantity));
    }
}
