<?php

namespace App\Helpers\API;
use App\Models\ToDosStatuses;
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

    public static function getRequestData($request){
        if(str_contains($request->header('Content-Type'), 'text/plain')){
            return $request->json()->all();
          }else{
              return $request->all();
          }
    }

    public static function checkExistParentTodo($request, $db){
        $requestData = self::getRequestData($request);
        if(intval($requestData['id_parent_todo']) == 0){
            return true;
        }
        $parentTodo = $db->find($requestData['id_parent_todo'])->first();
        if(!empty($requestData['id_parent_todo']) && 
        empty($parentTodo)) {
          return false;
        }
        if(!empty($requestData['id_parent_todo']) && 
        !empty($parentTodo) &&
        $requestData['id_user'] != $parentTodo['id_user']) {
            return false;
          }
          return true;
    }

    public static function checkTodoOwner($request, $db){
        $requestData = self::getRequestData($request);
        $todo = $db->find($requestData['id'])->first();
        if(intval($todo["id_user"]) != intval($requestData['id_user'])){
            return false;
        }
        return true;
    }

    public static function getStatusName($request, $db){
        $requestData = self::getRequestData($request);
        $toDosStatuses = new ToDosStatuses;
        $status = $toDosStatuses->find($requestData["id_status"])->first();
        if($status){
           return $status["name"];
        }else{
            return false;
        }
    }
    
}
