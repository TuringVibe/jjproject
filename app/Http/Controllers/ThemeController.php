<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateThemeChange;
use App\Services\ThemeService;

class ThemeController extends Controller {

    private $theme_service;

    public function __construct(ThemeService $theme_service)
    {
        $this->theme_service = $theme_service;
    }

    public function change(ValidateThemeChange $request) {
        $result = $this->theme_service->change($request->theme_id);
        return redirect()->back();
    }
}
