<?php

namespace App\Http\Requests\API\ToDos;

use Illuminate\Foundation\Http\FormRequest;

class ToDoCreateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'test' => 'required|int',
            // 'product_name' => 'required|string|min:3',
            // 'weigth' => 'decimal',
            // 'description' => 'string',
            // 'total_price' => 'required|decimal:2'
        ];
    }



    public $validator = null;

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
       $this->validator = $validator;
    }


}
