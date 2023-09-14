<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\API\ToDosHelper;
use App\Http\Requests\API\ToDos\ToDoCreateRequest;

class ToDosController extends Controller
{
    public function createToDo(ToDoCreateRequest $request):JsonResponse 
    {
        $this->requestValidateError = ToDosHelper::requestValidationErrorsData($request);
        if ($this->requestValidateError) { 
            return Controller::ApiResponceError($this->requestValidateError, 500); 
         }else{
            return Controller::apiResponceSuccess('test', 200);
            
    }
}

}