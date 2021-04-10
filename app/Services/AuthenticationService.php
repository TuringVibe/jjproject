<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthenticationService {

    public function login($email, $password, $remember = false) {
        $user = User::whereNull('deleted_at')->where('email',$email)->first();
        if($user AND Auth::attempt(['email' => $email, 'password' => $user->salt.$password], $remember)) {
            return true;
        }
        return false;
    }
}
