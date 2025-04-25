<?php

namespace App\Helpers;

use Laratrust\Contracts\LaratrustUser;

class PermissionsProcess
{
    const ROLE_ADMIN = "super_admin";
    const ROLE_USER = "user";

    private ?array $permissions = null;

    public function setPermissionsUserAuth(){
        $user = auth()->user();
        $this->permissions = ($user instanceOf LaratrustUser) ?
            $user->allPermissions(["name"])->pluck("name")->toArray()
            :
            [];
    }

    public function getPermissions(): array{
        return $this->permissions ?? [];
    }

    public function checkPermissionExists(string|array $permission ,bool $typeConditionOr = true): bool{
        if (!is_array($permission)){
            $permission = [$permission];
        }
        $temp = 0;
        foreach ($permission as $name){
            if (in_array($name,$this->getPermissions())){
                if ($typeConditionOr){
                    return true;
                }
                $temp++;
            }
        }
        return ( !$typeConditionOr && (count($permission) == $temp) );
    }

    public function addPermissions($permission){
        if (!is_array($permission)){
            $permission = [$permission];
        }
        $this->permissions = array_merge($this->getPermissions(),$permission);
    }
}
