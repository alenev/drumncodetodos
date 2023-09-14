<?php

namespace App\Helpers\API;

class ToDosHelper {

    public static function requestValidationErrorsData($request)
    {
        if(isset($request->validator) && $request->validator->fails()){

        $validationErrors = $request->validator->errors()->messages();

        $validationErrorsFirst = current((array)$validationErrors);

        return $validationErrorsFirst[0];

        }else{

            return false;

        }
    }
    
}
