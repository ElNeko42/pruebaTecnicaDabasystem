<?php

namespace App\Actions;

use App\Models\RoomType;
use App\Repositories\RoomTypeRepository;

class SaveRoomType
{
    public function __construct(private RoomTypeRepository $repository) {}
    public function create(array $data): RoomType { return $this->repository->create($data); }
    public function update(RoomType $roomType, array $data): RoomType { return $this->repository->update($roomType, $data); }
}
