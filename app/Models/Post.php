<?php

namespace App\Models;

use App\Exceptions\UnAuthorizedException;
use App\Helpers\MyApp;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }

    public function comments(){
        return $this->hasMany(PostComment::class,"post_id","id");
    }

    public function canProcess($process){
        $user = auth()->user();
        if ($this->user_id == $user->id || in_array($process,MyApp::main()->permissionsProcess->getPermissions())){
            return;
        }
        throw new UnAuthorizedException("User does not have any of the necessary access rights.");
    }
}
