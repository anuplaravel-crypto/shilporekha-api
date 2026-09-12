<?php

namespace Tests\Feature;

use App\Models\Style;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StyleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_list_styles(): void
    {
        Style::factory()->count(3)->create();

        $response = $this->getJson('/api/styles');

        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_guest_cannot_create_a_style(): void
    {
        $response = $this->postJson('/api/styles', ['name' => 'Minimalist']);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_a_style(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->postJson('/api/styles', ['name' => 'Minimalist']);

        $response->assertCreated()->assertJsonPath('data.slug', 'minimalist');
        $this->assertDatabaseHas('styles', ['slug' => 'minimalist']);
    }

    public function test_creating_a_duplicate_style_name_gets_a_unique_slug_suffix(): void
    {
        $this->actingAs(User::factory()->create());
        Style::factory()->create(['name' => 'Minimalist', 'slug' => 'minimalist']);

        $response = $this->postJson('/api/styles', ['name' => 'Minimalist']);

        $response->assertCreated()->assertJsonPath('data.slug', 'minimalist-1');
    }

    public function test_authenticated_user_can_update_and_delete_a_style(): void
    {
        $this->actingAs(User::factory()->create());
        $style = Style::factory()->create(['name' => 'Old Name']);

        $updated = $this->patchJson("/api/styles/{$style->id}", ['name' => 'New Name']);
        $updated->assertOk()->assertJsonPath('data.slug', 'new-name');

        $deleted = $this->deleteJson("/api/styles/{$style->id}");
        $deleted->assertOk()->assertJsonPath('status', true);
        $this->assertDatabaseMissing('styles', ['id' => $style->id]);
    }
}
