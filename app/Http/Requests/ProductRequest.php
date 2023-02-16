<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $required = !isset($this->product)? 'required' : 'sometimes';
        return [
            'name' => [$required,'string','between:2,100',\Illuminate\Validation\Rule::unique('products')->ignore($this->product)],
            'price' => [$required,'numeric'],
            'description' => [$required,'string','between:2,1000'],
            'category' => [$required,'string'],
            "image_url" => ['sometimes',function($attribute, $value, $invalid) {
                $updateImg = isset($this->product)? ", or null if you want to remove it.":".";
                return (
                    filter_var($value, FILTER_VALIDATE_URL) || is_null($value) ?:
                    $invalid("The image_url must be a valid URL".$updateImg)
                );
            }]
        ];
    }
}
