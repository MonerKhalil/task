<?php

namespace App\Observers;

use App\Events\PostCreated;
use App\Models\Post;

class PostObserver
{
    public function created(Post $post)
    {
        event(new PostCreated($post));
    }
}
