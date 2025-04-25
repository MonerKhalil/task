<?php

namespace App\Models;

use App\Exceptions\UnAuthorizedException;
use App\Helpers\MyApp;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class,"user_id","id");
    }

    public function post(){
        return $this->belongsTo(Post::class,"post_id","id");
    }
}
