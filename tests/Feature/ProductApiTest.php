<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Models\Style;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_guest_can_list_products_filtered_by_service(): void
    {
        Product::factory()->count(2)->create();
        $service = Service::factory()->create();
        Product::factory()->for($service)->count(3)->create();

        $response = $this->getJson("/api/products?service_id={$service->id}");

        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_guest_can_view_a_single_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $product->id)
            ->assertJsonPath('data.category.id', $product->category_id)
            ->assertJsonPath('data.subcategory.id', $product->subcategory_id);
    }

    public function test_guest_cannot_create_a_product(): void
    {
        $response = $this->postJson('/api/products', ['name' => 'Bass Strike Tee']);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_a_product_with_an_uploaded_image(): void
    {
        $this->actingAs(User::factory()->create());
        $service = Service::factory()->create();
        $category = Category::factory()->for($service)->create();
        $subcategory = Subcategory::factory()->for($category)->create();
        $style = Style::factory()->create();

        $response = $this->postJson('/api/products', [
            'service_id' => $service->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'style_id' => $style->id,
            'name' => 'Bass Strike Tee',
            'description' => 'A largemouth bass tee.',
            'price' => 24.99,
            'image' => UploadedFile::fake()->image('tee.jpg'),
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.slug', 'bass-strike-tee')
            ->assertJsonPath('data.price', '24.99');

        $product = Product::firstWhere('slug', 'bass-strike-tee');
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_creating_a_product_requires_an_image(): void
    {
        $this->actingAs(User::factory()->create());
        $service = Service::factory()->create();
        $category = Category::factory()->for($service)->create();
        $subcategory = Subcategory::factory()->for($category)->create();

        $response = $this->postJson('/api/products', [
            'service_id' => $service->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'name' => 'Bass Strike Tee',
            'price' => 24.99,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['image']);
    }

    public function test_creating_a_product_rejects_a_category_that_does_not_belong_to_the_service(): void
    {
        $this->actingAs(User::factory()->create());
        $service = Service::factory()->create();
        $otherServicesCategory = Category::factory()->create(); // different service
        $subcategory = Subcategory::factory()->for($otherServicesCategory)->create();

        $response = $this->postJson('/api/products', [
            'service_id' => $service->id,
            'category_id' => $otherServicesCategory->id,
            'subcategory_id' => $subcategory->id,
            'name' => 'Bass Strike Tee',
            'price' => 24.99,
            'image' => UploadedFile::fake()->image('tee.jpg'),
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['category_id']);
    }

    public function test_creating_a_product_rejects_a_subcategory_that_does_not_belong_to_the_category(): void
    {
        $this->actingAs(User::factory()->create());
        $service = Service::factory()->create();
        $category = Category::factory()->for($service)->create();
        $otherCategorysSubcategory = Subcategory::factory()->create(); // different category

        $response = $this->postJson('/api/products', [
            'service_id' => $service->id,
            'category_id' => $category->id,
            'subcategory_id' => $otherCategorysSubcategory->id,
            'name' => 'Bass Strike Tee',
            'price' => 24.99,
            'image' => UploadedFile::fake()->image('tee.jpg'),
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['subcategory_id']);
    }

    public function test_authenticated_user_can_update_a_product_and_replace_its_image(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create(['image_path' => 'products/old.jpg']);
        Storage::disk('public')->put('products/old.jpg', 'fake-contents');

        $response = $this->postJson("/api/products/{$product->id}", [
            '_method' => 'PUT',
            'name' => 'Updated Name',
            'image' => UploadedFile::fake()->image('new.jpg'),
        ]);

        $response->assertOk()->assertJsonPath('data.name', 'Updated Name');

        $product->refresh();
        Storage::disk('public')->assertExists($product->image_path);
        Storage::disk('public')->assertMissing('products/old.jpg');
    }

    public function test_authenticated_user_can_delete_a_product_and_its_image_is_removed(): void
    {
        $this->actingAs(User::factory()->create());
        $product = Product::factory()->create(['image_path' => 'products/delete-me.jpg']);
        Storage::disk('public')->put('products/delete-me.jpg', 'fake-contents');

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertOk()->assertJsonPath('status', true);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('products/delete-me.jpg');
    }
}
