<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', Rule::unique('products', 'name')->ignore($this->product)],
            'description' => ['nullable', 'string'],
            'initial_stock' => ['nullable', 'numeric'],
            'category' => ['required'],
            'unit_id' => ['required', 'numeric'],
            'image' => ['nullable'],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nama produk',
            'description' => 'deskripsi',
            'initial_stock' => 'stok awal',
            'unit_id' => 'unit',
            'image' => 'gambar produk',
            'category' => 'kategori',
        ];
    }
}
