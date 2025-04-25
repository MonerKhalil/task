<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Jobs\SendPostCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PostCreatedListener implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  PostCreated  $event
     * @return void
     */
    public function handle(PostCreated $event)
    {
        SendPostCreatedNotification::dispatch($event->post);
    }
}
