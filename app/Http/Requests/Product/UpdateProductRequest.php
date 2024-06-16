<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() ? true : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product_id = $this->route('product');

        $ruleUnique = Rule::unique('products', 'name')->where('store_id', $this->input('store_id'))->whereNot('id', $product_id);
        $ruleStoreExists = Rule::exists('stores', 'id')->where('user_id', $this->user()->id);
        return [
            'store_id' => ['bail', 'required', $ruleStoreExists],
            'name' => ['bail', 'required', 'min:8', $ruleUnique],
            'amount' => ['bail', 'required', 'integer', 'gte:0'],
            'price' => ['bail', 'required', 'numeric', 'gt:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'store_id.exists' => 'The selected store id is invalid for current user',
        ];
    }
}
