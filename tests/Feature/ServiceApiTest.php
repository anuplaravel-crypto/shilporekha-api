<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_list_services(): void
    {
        Service::factory()->count(2)->create();

        $response = $this->getJson('/api/services');

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonCount(2, 'data');
    }

    public function test_guest_can_view_a_single_service_with_subcategories(): void
    {
        $service = Service::factory()->create();
        $service->subcategories()->create(['name' => 'Fishing', 'slug' => 'fishing', 'sort_order' => 0]);

        $response = $this->getJson("/api/services/{$service->id}");

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.id', $service->id)
            ->assertJsonCount(1, 'data.subcategories');
    }

    public function test_viewing_a_missing_service_returns_standard_404_envelope(): void
    {
        $response = $this->getJson('/api/services/999');

        $response->assertStatus(404)
            ->assertJsonPath('status', false);
    }

    public function test_guest_cannot_create_a_service(): void
    {
        $response = $this->postJson('/api/services', ['name' => 'Branding', 'status' => 'active']);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_a_service_with_an_auto_generated_slug(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->postJson('/api/services', [
            'name' => 'Branding',
            'status' => 'active',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.slug', 'branding')
            // Regression: a fresh service has no subcategories loaded by
            // default, so ServiceResource's whenLoaded() previously omitted
            // the key entirely instead of returning an empty array — which
            // crashed the admin UI's `service.subcategories.length`.
            ->assertJsonPath('data.subcategories', []);

        $this->assertDatabaseHas('services', ['name' => 'Branding', 'slug' => 'branding']);
    }

    public function test_creating_a_service_with_a_duplicate_name_gets_a_unique_slug_suffix(): void
    {
        $this->actingAs(User::factory()->create());
        Service::factory()->create(['name' => 'Branding', 'slug' => 'branding']);

        $response = $this->postJson('/api/services', [
            'name' => 'Branding',
            'status' => 'active',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.slug', 'branding-1');
    }

    public function test_creating_a_service_validates_required_fields(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->postJson('/api/services', []);

        $response->assertStatus(422)
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'Validation Failed')
            ->assertJsonValidationErrors(['name', 'status']);
    }

    public function test_authenticated_user_can_update_a_service(): void
    {
        $this->actingAs(User::factory()->create());
        $service = Service::factory()->create(['status' => 'coming_soon']);

        $response = $this->patchJson("/api/services/{$service->id}", ['status' => 'active']);

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('services', ['id' => $service->id, 'status' => 'active']);
    }

    public function test_authenticated_user_can_delete_a_service(): void
    {
        $this->actingAs(User::factory()->create());
        $service = Service::factory()->create();

        $response = $this->deleteJson("/api/services/{$service->id}");

        $response->assertOk()->assertJsonPath('status', true);

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }
}
