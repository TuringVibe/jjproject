<?php

namespace Database\Seeders;

use App\Models\Theme;
use App\Models\ThemeUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $light = Theme::create([
            'name' => 'Light',
            'css_path' => 'css/theme-light.css',
            'is_default' => false
        ]);
        $dark = Theme::create([
            'name' => 'Dark',
            'css_path' => 'css/theme-dark.css',
            'is_default' => true
        ]);
        foreach(User::cursor() as $user) {
            ThemeUser::create([
                'user_id' => $user->id,
                'theme_id' => $dark->id
            ]);
        }
    }
}
