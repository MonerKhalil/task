<?php

namespace App\Http\Controllers\Apis;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Validation\Rules\Password;
use App\Http\Resources\UserProfileResource;
use App\Http\Controllers\BaseCrudController;

class UserController extends BaseCrudController
{
    public function __construct()
    {
        $this->middleware(["permissions:manage_users|show_users"])->only(["index","find"]);
        $this->middleware(["permissions:manage_users|create_users"])->only(["store"]);
        $this->middleware(["permissions:manage_users|update_users"])->only(["update","changePassword"]);
        $this->middleware(["permissions:manage_users|delete_users"])->only(["delete"]);
    }

    protected function getMainModel()
    {
        return User::class;
    }

    protected function storeRequest()
    {
        return UserRequest::class;
    }

    protected function editRequest()
    {
        return UserRequest::class;
    }

    protected function resource():?string{
        return UserProfileResource::class;
    }

    protected function fieldsSearchLike():array{
        return ["name","role","first_name","last_name","email","phone","address"];
    }

    protected function fieldsFiles(): array{
        return ["image"];
    }

    protected function resolveDataStore($data){
        $data['email_verified_at'] = now();
        return $data;
    }

    protected function createdObserver($objModel,$data){
        $this->mainObserverUser($objModel,$data);
    }

    protected function updatedObserver($objModel,$data){
        $this->mainObserverUser($objModel,$data);
    }

    private function mainObserverUser($objModel,$data){
        $role = $data['role'];
        $role = Role::query()->where("name",$role)->first();
        if (!is_null($role)){
            $objModel->roles()->sync([$role->id]);
        }
    }

    public function changePassword($id,Request $request){
        $request->validate([
            "password" => ['required', 'string', Password::default(),],
        ]);
        $user = User::query()->findOrFail($id);
        $user->update([
            "password" => $request->password,
        ]);
        return $this->responseSuccess([],"Password updated successfully");
    }

}
