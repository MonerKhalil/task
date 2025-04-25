<?php

namespace App\Jobs;

use App\Helpers\PermissionsProcess;
use App\Mail\PostCreatedMail;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPostCreatedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Post $post)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user_id = auth()->id();
        $emails = User::query()
            ->whereNot("id",$user_id)
            ->where("role",PermissionsProcess::ROLE_ADMIN)
            ->get()
            ->pluck("emails")
            ->toArray();
        foreach ($emails as $email){
            Mail::to($email)->send(new PostCreatedMail($this->post));
        }
    }
}
