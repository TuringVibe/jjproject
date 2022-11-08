<?php

namespace App\Http\Middleware;

use App\Models\Theme;
use App\Models\ThemeUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThemeCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!session()->has('theme')) {
            if(Auth::check()) {
                $user_id = Auth::user()->id;
                $theme_user = $this->detectUserTheme($user_id);
                $theme = $theme_user->theme;
            } else {
                $user_id = null;
                $theme = Theme::where('is_default',true)->first();
            }

            session(['theme' => [
                'user_id' => $user_id,
                'obj' => $theme
            ]]);
        } else {
            $theme = session('theme');
            if(Auth::check() AND $theme['user_id'] == null) {
                $user_id = Auth::user()->id;
                $theme_user = $this->detectUserTheme($user_id);
                $theme = $theme_user->theme;
                session()->put('theme', [
                    'user_id' => $user_id,
                    'obj' => $theme
                ]);
            }
        }
        return $next($request);
    }

    private function detectUserTheme($user_id) {
        $theme_user = ThemeUser::where('user_id',$user_id)->first();
        if($theme_user == null) {
            $theme_user = ThemeUser::create([
                'user_id' => $user_id,
                'theme_id' => Theme::where('is_default',true)->first()->id
            ]);
        }
        return $theme_user;
    }
}
