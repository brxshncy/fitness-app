<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => Role::ADMIN])->first();

        $user = User::factory()->create([
            'email'=> 'admin@admin.com', 
            'password'=> Hash::make('admin'), 
            'name' => 'Admin'
        ]);

        if ($admin) {   
            $user->assignRole($admin);
        }

    }
}
