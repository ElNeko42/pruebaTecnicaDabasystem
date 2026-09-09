<?php

namespace Tests\Feature;

use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomTypeIsFeaturedTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_create_or_update_room_types(): void
    {
        $roomType = RoomType::factory()->create();

        $this->postJson('/api/admin/room-types', ['name' => 'Suite', 'is_featured' => true])->assertUnauthorized();
        $this->putJson("/api/admin/room-types/{$roomType->id}", ['name' => 'Suite', 'is_featured' => true])->assertUnauthorized();

        $this->assertDatabaseMissing('room_types', ['name' => 'Suite']);
        $this->assertFalse($roomType->refresh()->is_featured);
    }

    public function test_is_featured_is_persisted_on_create_and_update(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $created = $this->postJson('/api/admin/room-types', ['name' => 'Suite', 'is_featured' => true])
            ->assertCreated()
            ->assertJsonPath('data.is_featured', true);

        $id = $created->json('data.id');
        $this->assertTrue(RoomType::find($id)->is_featured);

        $this->putJson("/api/admin/room-types/{$id}", ['name' => 'Suite', 'is_featured' => false])
            ->assertOk()
            ->assertJsonPath('data.is_featured', false);

        $this->assertFalse(RoomType::find($id)->is_featured);
    }

    public function test_public_endpoint_exposes_a_real_boolean_and_no_internal_fields(): void
    {
        RoomType::factory()->create(['name' => 'Suite', 'is_featured' => true]);

        $response = $this->getJson('/api/front/room-types')->assertOk();

        $this->assertSame(['id', 'name', 'is_featured'], array_keys($response->json('data.0')));
        $this->assertTrue($response->json('data.0.is_featured'));
    }
}
