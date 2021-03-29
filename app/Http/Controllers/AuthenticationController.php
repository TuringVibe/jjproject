<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateForgetPassword;
use App\Http\Requests\ValidateLogin;
use App\Http\Requests\ValidateResetPassword;
use App\Services\AuthenticationService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{

    private $auth_service;

    public function __construct(AuthenticationService $auth_service)
    {
        $this->auth_service = $auth_service;
    }

    public function login() {
        $this->config['basic'] = true;
        return view("pages.login", $this->config);
    }

    public function doLogin(ValidateLogin $request) {
        $result = $this->auth_service->login($request->email, $request->password, $request->remember_me);
        if($result) {
            $request->session()->regenerate();
            return redirect()->intended('project/dashboard');
        }
        return back()->with("error",__("auth.failed"));
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function forgetPassword() {
        $this->config['basic'] = true;
        return view("pages.forget-password", $this->config);
    }

    public function sendLinkResetPassword(ValidateForgetPassword $request) {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with('success',__($status))
                    : back()->with('error',__($status));
    }

    public function resetPassword(Request $request) {
        $this->config['basic'] = true;
        $this->config['token'] = $request->query('token');
        $this->config['email'] = $request->query('email');
        return view("pages.reset-password", $this->config);
    }

    public function doResetPassword(ValidateResetPassword $request) {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($user->salt.$password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('success', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
