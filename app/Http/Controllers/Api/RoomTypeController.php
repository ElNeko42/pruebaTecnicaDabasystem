<?php

namespace App\Http\Controllers\Api;

use App\Actions\SaveRoomType;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoomTypeRequest;
use App\Http\Resources\RoomTypeResource;
use App\Models\RoomType;
use App\Repositories\RoomTypeRepository;

class RoomTypeController extends Controller
{
    public function index(RoomTypeRepository $repository) { $this->authorize('viewAny', RoomType::class); return RoomTypeResource::collection($repository->all()); }
    public function store(RoomTypeRequest $request, SaveRoomType $action) { return (new RoomTypeResource($action->create($request->validated())))->response()->setStatusCode(201); }
    public function update(RoomTypeRequest $request, RoomType $roomType, SaveRoomType $action) { return new RoomTypeResource($action->update($roomType, $request->validated())); }
    public function publicIndex(RoomTypeRepository $repository) { return RoomTypeResource::collection($repository->all()); }
}
