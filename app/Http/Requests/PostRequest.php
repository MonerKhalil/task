<?php

namespace App\Http\Requests;

use App\Helpers\MyApp;

class PostRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $id = $this->route("id");
        return [
            "title" => ["required","string","max:255"],
            "content" => ["required","string"],
            "image" => MyApp::main()->crudProcess->ruleImage(is_null($id)),
        ];
    }
}
