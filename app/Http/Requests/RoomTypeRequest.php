<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomTypeRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can($this->isMethod('post') ? 'create' : 'update', $this->route('room_type')) ?? false; }
    public function rules(): array { return ['name' => ['required', 'string', 'max:120']]; }
}
