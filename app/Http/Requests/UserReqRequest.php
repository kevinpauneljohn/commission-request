<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserReqRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(Request $request): array
    {
        return [
            'sales_director' => [Rule::requiredIf(!auth()->user()->hasRole('sales director'))],
            'firstname' => ['required'],
            'lastname' => ['required'],
            'project' => ['required'],
            'model_unit' => ['required'],
            'block' => ['required'],
            'lot' => ['required'],
            'total_contract_price' => ['required'],
            'financing' => ['required'],
            'request_type' => ['required'],
            'sd_rate' => ['required'],
            'cheque_number' => ['numeric','nullable'],
//            'bank_name' => [Rule::requiredIf($request->request_type === "cheque_pickup")],
//            'cheque_amount' => [Rule::requiredIf($request->request_type === "cheque_pickup")],
        ];
    }
}
