<?php

namespace App\Http\Resources;

use App\Helpers\MyApp;
use Illuminate\Http\Resources\Json\JsonResource;

class PostsResource extends JsonResource
{
    public function toArray($request = null)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'full_name' => $this->user->name,
                'image' => MyApp::main()->fileProcess->getFullLink($this->user->image),
            ],
            'comments_count' => $this->comments_count ?? 0,
            'image' => MyApp::main()->fileProcess->getFullLink($this->image),
            'created_at' => $this->created_at,
        ];
    }
}
