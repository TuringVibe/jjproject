<?php
namespace App\Http\Controllers;

use App\Http\Requests\ValidateUser;
use App\Http\Requests\ValidateUserId;
use App\Http\Requests\ValidateUserParams;
use App\Services\UserService;

class UserController extends Controller {
    private $user_service;

    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    public function list() {
        $this->config['title'] = "USER MANAGEMENT";
        $this->config['active'] = "users.list";
        $this->config['navs'] = [
            [
                'label' => 'User Management'
            ]
        ];
        return view('pages.user-list', $this->config);
    }

    public function data(ValidateUserParams $request) {
        $params['name'] = $request->name;
        $params['role'] = $request->role;
        $result = $this->user_service->get($params);
        echo json_encode($result);
    }

    public function detail(ValidateUserId $request) {
        $result = $this->user_service->detail($request->id);
        return $result;
    }

    public function save(ValidateUser $request) {
        $attr = $request->validated();
        if(isset($attr['img'])) {
            $attr['img_path'] = $request->file('img')->storePublicly('users','public');
        }
        if(array_key_exists('img',$attr)) unset($attr['img']);
        $result = $this->user_service->save($attr);
        return $result;
    }

    public function delete(ValidateUserId $request) {
        $result = $this->user_service->delete($request->id);
        return $result;
    }
}
