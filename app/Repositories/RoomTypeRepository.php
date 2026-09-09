<?php

namespace App\Repositories;

use App\Models\RoomType;

class RoomTypeRepository
{
    public function all() { return RoomType::query()->orderBy('name')->get(); }
    public function create(array $data): RoomType { return RoomType::create($data); }
    public function update(RoomType $roomType, array $data): RoomType { $roomType->update($data); return $roomType->refresh(); }
}
