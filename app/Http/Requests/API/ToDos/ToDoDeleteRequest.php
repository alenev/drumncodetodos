<?php

namespace App\Http\Requests\API\ToDos;

use Illuminate\Foundation\Http\FormRequest;

class ToDoDeleteRequest extends FormRequest
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
            'id' => 'required|int|exists:todos,id',
            'id_user' => 'required|int|exists:users,id'
        ];
    }



    public $validator = null;

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
       $this->validator = $validator;
    }


}
