<?php

namespace App\Services;

use App\Models\ThemeUser;
use Illuminate\Support\Facades\Auth;

class ThemeService {
    public function change($dest_theme_id) {
        $user_id = Auth::user()->id;
        $theme_user = ThemeUser::where('user_id',$user_id)->first();
        $theme_user->theme_id = $dest_theme_id;
        $result = $theme_user->save();
        if($result) {
            session()->put('theme',[
                'user_id' => $user_id,
                'obj' => $theme_user->theme
            ]);
        }
        return $result;
    }
}
