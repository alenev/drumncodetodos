<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\ToDosRepository;
use App\Helpers\API\ToDosHelper;
use App\Http\Requests\API\ToDos\ToDoCreateRequest;
use App\Http\Requests\API\ToDos\ToDosGetRequest;


class ToDosController extends Controller
{
    private ToDosRepository $db;
    private object $todo;
    private $todos;
    private $requestData;

    public function __construct(ToDosRepository $db){
        $this->db = $db;
   }

    public function createToDo(ToDoCreateRequest $request):JsonResponse 
    {
        $this->requestValidateError = ToDosHelper::requestValidationErrorsData($request);

        if ($this->requestValidateError) { 
            return Controller::ApiResponceError($this->requestValidateError, 500); // no valid input data
         }else{
            $this->requestData = ToDosHelper::getRequestData($request);        
            if(!ToDosHelper::checkExistParentTodo($request, $this->db)) {
              return Controller::ApiResponceError('parent todo not found', 404); // parent todo has another owne
            }

            $this->todo = $this->db->create($this->requestData);
            if(!empty($this->todo)){
                return Controller::ApiResponceSuccess([
                    "message" => "todo created",
                    "data" => $this->todo
                ], 200);  
            }else{
                return Controller::ApiResponceError('creating todo problem', 500); 
            }
         }
    }

    public function getToDos(ToDosGetRequest $request):JsonResponse
    {

        $this->requestValidateError = ToDosHelper::requestValidationErrorsData($request);

        if ($this->requestValidateError) { 
            return Controller::ApiResponceError($this->requestValidateError, 500); 
         }else{
            $this->requestData = ToDosHelper::getRequestData($request);
            $this->todos = $this->db->getPaginated($this->requestData);
            if($this->todos){
                return Controller::ApiResponceSuccess([
                    "message" => "ToDos selected",
                    "data" => $this->todos
                ], 200);  
            }
         }
    }

    public function showToDo($id)
    {
        $this->todo = $this->db->find($id)->first();
    }

}