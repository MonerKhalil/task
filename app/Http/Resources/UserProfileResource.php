<?php

namespace App\Http\Resources;

use App\Helpers\MyApp;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    public function toArray($request = null)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'image' => MyApp::main()->fileProcess->getFullLink($this->image),
            'address' => $this->address,
            'created_at' => $this->created_at?->format('Y-m-d'),
        ];
    }
}
