<?php

namespace Tests\Feature;

use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomTypeBaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_room_types(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        RoomType::factory()->create(['name' => 'Suite Demo']);
        $this->getJson('/api/admin/room-types')->assertOk()->assertJsonPath('data.0.name', 'Suite Demo');
    }

    public function test_public_endpoint_returns_only_the_public_shape(): void
    {
        RoomType::factory()->create(['name' => 'Doble Demo']);
        $this->getJson('/api/front/room-types')->assertOk()->assertJsonStructure(['data' => [['id', 'name']]])->assertJsonMissing(['created_at', 'updated_at']);
    }
}
