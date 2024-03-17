<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function after(Request $request)
    {
        return [
            function (Validator $validator) use ($request) {
                if ($request->isMethod('post')) {
                    if(DB::table('users')->where('email',$request->email)->count() > 0)
                    {
                        $validator->errors()->add(
                            'email',
                            'The email was already taken!'
                        );
                    }

                    if(DB::table('users')->where('username',$request->username)->count() > 0)
                    {
                        $validator->errors()->add(
                            'username',
                            'The username was already taken!'
                        );
                    }

                }
            }
        ];
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(Request $request): array
    {
        return [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => [Rule::requiredIf(!$request->has('user_id')),'email'],
            'username' => [Rule::requiredIf(!$request->has('user_id'))],
            'password' => [Rule::requiredIf(!$request->has('user_id')), 'confirmed'],
            'role' => 'required'
        ];
    }
}
