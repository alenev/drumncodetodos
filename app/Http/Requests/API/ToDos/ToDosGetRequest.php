<?php

namespace App\Http\Requests\API\ToDos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ToDosGetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

     public function validationData()
     {
        if(str_contains($this->header('Content-Type'), 'text/plain')){
          return $this->json()->all();
        }else{
            return $this->all();
        }
    
     }

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
            'id_user' => 'required|int|exists:users,id',
            'id_parent_todo' => 'int',
            'id_status' => 'int',
            'priority' => 'string|regex:/[,]+/'
        ];
    }



    public $validator = null;

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
       $this->validator = $validator;
    }


}
