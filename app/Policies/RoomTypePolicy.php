<?php

namespace App\Policies;

use App\Models\RoomType;
use App\Models\User;

class RoomTypePolicy
{
    public function viewAny(User $user): bool { return $user->hasPermission('room-types.view'); }
    public function create(User $user): bool { return $user->hasPermission('room-types.manage'); }
    public function update(User $user, RoomType $roomType): bool { return $user->hasPermission('room-types.manage'); }
}
