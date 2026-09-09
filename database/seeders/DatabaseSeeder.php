<?php

namespace Database\Seeders;

use App\Models\RoomType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@example.test'], ['name' => 'Admin Demo', 'password' => Hash::make('password'), 'role' => 'admin']);
        foreach (['Individual', 'Doble', 'Suite', 'Familiar'] as $name) RoomType::firstOrCreate(['name' => $name]);
    }
}
