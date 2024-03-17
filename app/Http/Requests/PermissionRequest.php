<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;
use Spatie\Permission\Contracts\Permission;

class PermissionRequest extends FormRequest
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
                    if(DB::table('permissions')->where('name',$request->permission)->count() > 0)
                    {
                        $validator->errors()->add(
                            'permission',
                            'The permission was already taken!'
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
    public function rules(): array
    {
        return [
            'permission' => ['required'],
        ];
    }
}
