<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_list_categories_filtered_by_service(): void
    {
        $service = Service::factory()->create();
        Category::factory()->for($service)->count(2)->create();
        Category::factory()->count(3)->create(); // belong to other services

        $response = $this->getJson("/api/categories?service_id={$service->id}");

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonCount(2, 'data');
    }

    public function test_guest_can_view_a_single_category_with_subcategories(): void
    {
        $category = Category::factory()->create();
        $category->subcategories()->create(['name' => 'Fishing', 'slug' => 'fishing', 'sort_order' => 0]);

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonCount(1, 'data.subcategories');
    }

    public function test_guest_cannot_create_a_category(): void
    {
        $response = $this->postJson('/api/categories', ['name' => 'Streetwear']);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_a_category_with_an_auto_generated_slug(): void
    {
        $this->actingAs(User::factory()->create());
        $service = Service::factory()->create();

        $response = $this->postJson('/api/categories', [
            'service_id' => $service->id,
            'name' => 'Streetwear',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.slug', 'streetwear')
            ->assertJsonPath('data.subcategories', []);

        $this->assertDatabaseHas('categories', ['service_id' => $service->id, 'slug' => 'streetwear']);
    }

    public function test_creating_a_category_validates_required_fields(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->postJson('/api/categories', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['service_id', 'name']);
    }

    public function test_authenticated_user_can_update_a_category(): void
    {
        $this->actingAs(User::factory()->create());
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->patchJson("/api/categories/{$category->id}", ['name' => 'New Name']);

        $response->assertOk()->assertJsonPath('data.slug', 'new-name');
    }

    public function test_authenticated_user_can_delete_a_category(): void
    {
        $this->actingAs(User::factory()->create());
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertOk()->assertJsonPath('status', true);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
