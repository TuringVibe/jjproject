<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $salt = Str::random(10);
        $now = Carbon::now();
        DB::table('users')->insert([
            'firstname' => 'Jeffrey',
            'lastname' => 'Joson',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'salt' => $salt,
            'password' => Hash::make($salt.'admin123'),
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
