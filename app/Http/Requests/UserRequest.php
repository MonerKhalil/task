<?php

namespace App\Http\Requests;

use App\Helpers\MyApp;
use App\Helpers\PermissionsProcess;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $id = $this->route("id");
        $ruleEmail = !is_null($id) ?
            ['required','email',Rule::unique("users","email")->ignore($id)]
            :
            ['required','email',Rule::unique("users","email")];
        $rulePhone = !is_null($id) ?
            ['required','integer','regex:/^[0-9]{7,15}$/i',Rule::unique("users","phone")->ignore($id)]
            :
            ['required','integer','regex:/^[0-9]{7,15}$/i',Rule::unique("users","phone")];
        $rules = [
            'role' => ['required','string',Rule::in(PermissionsProcess::ROLE_ADMIN,PermissionsProcess::ROLE_USER)],
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'phone' => $rulePhone,
            'email' => $ruleEmail,
            'image' => MyApp::main()->crudProcess->ruleImage(false),
            'address' => ['nullable','string','max:255'],
        ];
        if (is_null($id)){
            $rules['password'] = ['required', 'string', Password::default(),];
        }
        return $rules;
    }
}
