<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubcategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_list_subcategories_filtered_by_category(): void
    {
        $category = Category::factory()->create();
        Subcategory::factory()->for($category)->count(2)->create();
        Subcategory::factory()->count(3)->create(); // belong to other categories

        $response = $this->getJson("/api/subcategories?category_id={$category->id}");

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_guest_cannot_create_a_subcategory(): void
    {
        $response = $this->postJson('/api/subcategories', ['name' => 'Fishing']);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_a_subcategory(): void
    {
        $this->actingAs(User::factory()->create());
        $category = Category::factory()->create();

        $response = $this->postJson('/api/subcategories', [
            'category_id' => $category->id,
            'name' => 'Fishing',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.slug', 'fishing')
            ->assertJsonPath('data.category_id', $category->id);

        $this->assertDatabaseHas('subcategories', ['category_id' => $category->id, 'slug' => 'fishing']);
    }

    public function test_creating_a_subcategory_validates_required_fields(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->postJson('/api/subcategories', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['category_id', 'name']);
    }

    public function test_authenticated_user_can_update_and_delete_a_subcategory(): void
    {
        $this->actingAs(User::factory()->create());
        $subcategory = Subcategory::factory()->create(['name' => 'Old Name']);

        $updated = $this->patchJson("/api/subcategories/{$subcategory->id}", ['name' => 'New Name']);
        $updated->assertOk()->assertJsonPath('data.slug', 'new-name');

        $deleted = $this->deleteJson("/api/subcategories/{$subcategory->id}");
        $deleted->assertOk()->assertJsonPath('status', true);
        $this->assertDatabaseMissing('subcategories', ['id' => $subcategory->id]);
    }
}
