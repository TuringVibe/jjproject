<?php
namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService {

    public function get($params = []) {
        $query_builder = User::whereNull('deleted_at');
        foreach($params as $field => $val) {
            if(isset($val)) {
                if($field == 'name') {
                    $query_builder->whereRaw('concat(firstname," ",lastname) like ?',["%{$val}%"]);
                } else {
                    $query_builder->where($field,$val);
                }
            }
        }
        $users = $query_builder->select('id','firstname','lastname','email','role')
            ->withCount('projects')->get()->toArray();

        return $users;
    }

    public function detail($id) {
        $user = User::find($id);
        return $user;
    }

    public function save($attr) {
        if(isset($attr['id'])) {
            $user = User::find($attr['id']);
            if(array_key_exists('password', $attr)) {
                if(!empty($attr['password'])) {
                    $attr['password'] = Hash::make($user->salt.$attr['password']);
                } else unset($attr['password']);
            }
            $user->fill($attr);
            $user->save();
        } else {
            $attr['salt'] = Str::random(10);
            $attr['password'] = Hash::make($attr['salt'].$attr['password']);
            $user = User::create($attr);
        }
        return $user;
    }

    public function delete($id) {
        $user = User::find($id);
        $user->deleted_by = Auth::user()->id;
        $user->deleted_at = Carbon::now();
        return $user->save();
    }

}
