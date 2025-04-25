<?php

namespace App\Http\Resources;

use App\Helpers\MyApp;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentsResource extends JsonResource
{
    public function toArray($request = null)
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'full_name' => $this->user->name,
                'image' => MyApp::main()->fileProcess->getFullLink($this->user->image),
            ],
            'comment' => $this->comment,
            'created_at' => $this->created_at,
        ];
    }
}
