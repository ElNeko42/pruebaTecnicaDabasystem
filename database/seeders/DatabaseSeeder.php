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
        foreach (['Individual' => false, 'Doble' => false, 'Suite' => true, 'Familiar' => false] as $name => $isFeatured) RoomType::updateOrCreate(['name' => $name], ['is_featured' => $isFeatured]);
    }
}
