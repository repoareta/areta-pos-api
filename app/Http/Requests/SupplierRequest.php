<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required_without:status,null|string',
            'email' => 'required_without:status,null|email|unique:suppliers,email',
            'phone_number' => 'required_without:status,null|min:10',
            'address' => 'nullable',
            'status' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'required_without' => "The :attribute field is required",
        ];
    }
}
