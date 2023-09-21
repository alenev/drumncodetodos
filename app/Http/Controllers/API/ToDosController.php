<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\ToDosRepository;
use App\Helpers\API\ToDosHelper;
use App\Http\Requests\API\ToDos\ToDoCreateRequest;
use App\Http\Requests\API\ToDos\ToDosGetRequest;
use App\Http\Requests\API\ToDos\ToDoUpdateRequest;
use App\Http\Requests\API\ToDos\ToDoDeleteRequest;
use Illuminate\Support\Carbon;

class ToDosController extends Controller
{
    private ToDosRepository $db;
    private object $todo;
    private $todos;
    private $requestData;

    public function __construct(ToDosRepository $db)
    {
        $this->db = $db;
    }

    public function createToDo(ToDoCreateRequest $request): JsonResponse
    {
        $this->requestValidateError = ToDosHelper::requestValidationErrorsData($request);

        if ($this->requestValidateError) {
            return Controller::ApiResponceError($this->requestValidateError, 500); // no valid input data
        } else {
            $this->requestData = ToDosHelper::getRequestData($request);
            if (!ToDosHelper::checkExistParentTodo($request, $this->db)) {
                return Controller::ApiResponceError('parent todo not found', 404); // parent todo has another owner
            }

            $this->todo = $this->db->create($this->requestData);
            if (!empty($this->todo)) {
                return Controller::ApiResponceSuccess([
                    "message" => "todo created",
                    "data" => $this->todo
                ], 200);
            } else {
                return Controller::ApiResponceError('creating todo problem', 500);
            }
        }
    }

    public function getToDos(ToDosGetRequest $request): JsonResponse
    {

        $this->requestValidateError = ToDosHelper::requestValidationErrorsData($request);

        if ($this->requestValidateError) {
            return Controller::ApiResponceError($this->requestValidateError, 500);
        } else {
            $this->requestData = ToDosHelper::getRequestData($request);
            if (array_key_exists('child_todos', $this->requestData) && $this->requestData["child_todos"] > 0) {
                $this->todos = $this->db->getChilds($this->requestData["child_todos"]);
                if ($this->todos) {
                    return Controller::ApiResponceSuccess([
                        "message" => "child todos",
                        "data" => $this->todos
                    ], 200);
                } else {
                    return Controller::ApiResponceError('child todos selection problem', 500);
                }
            }

            $this->todos = $this->db->getAll($this->requestData);


            return Controller::ApiResponceSuccess([
                "message" => "ToDos selected",
                "data" => $this->todos
            ], 200);

        }
    }

    public function updateToDo(ToDoUpdateRequest $request): JsonResponse
    {
        $this->requestValidateError = ToDosHelper::requestValidationErrorsData($request);

        if ($this->requestValidateError) {
            return Controller::ApiResponceError($this->requestValidateError, 500); // no valid input data
        } else {
            $this->requestData = ToDosHelper::getRequestData($request);

            if (intval($this->requestData["id_status"]) != 0) {
                $statusName = ToDosHelper::getStatusName($this->requestData, $this->db);
                if ($statusName == 'done') {
                    $checkChildsStatuseDifferense = ToDosHelper::checkUpdatePossibility($this->requestData, $this->db);
                    if (!empty($checkChildsStatuseDifferense)) {
                        return Controller::ApiResponceError('update todo status is blocked', 424); // update todo status is blocked
                    } else {
                        $this->requestData["completed_at"] = Carbon::now();
                    }
                } else {
                    $this->requestData["completed_at"] = null;
                }
            }

            if (!ToDosHelper::checkExistParentTodo($request, $this->db)) {
                return Controller::ApiResponceError('parent todo not found', 404); // parent todo has another owner
            }
            if (!ToDosHelper::checkTodoOwner($request, $this->db)) {
                return Controller::ApiResponceError('another owner of todo', 423); // another owner of todo 
            }

            $this->todo = $this->db->update($this->requestData, $this->requestData["id"]);
            if (!empty($this->todo)) {
                return Controller::ApiResponceSuccess([
                    "message" => "todo updated",
                    "data" => $this->todo
                ], 200);
            } else {
                return Controller::ApiResponceError('updating todo problem', 500);
            }
        }
    }

    public function deleteToDo(ToDoDeleteRequest $request): JsonResponse
    {
        $this->requestValidateError = ToDosHelper::requestValidationErrorsData($request);

        if ($this->requestValidateError) {
            return Controller::ApiResponceError($this->requestValidateError, 500); // no valid input data
        } else {
            $this->requestData = ToDosHelper::getRequestData($request);
            if (!ToDosHelper::checkTodoOwner($request, $this->db)) {
                return Controller::ApiResponceError('another owner of todo', 423); // another owner of todo 
            }
            $checkToDoDeleteDisable = ToDosHelper::checkToDoDeleteDisable($request, $this->db);
            if ($checkToDoDeleteDisable) {
                return Controller::ApiResponceError($checkToDoDeleteDisable, 423); // another owner of todo 
            } else {
                $deleting = $this->db->delete($this->requestData["id"]);
                if ($deleting) {
                    return Controller::ApiResponceSuccess([
                        "message" => "todo deleted",
                        "data" => []
                    ], 200);
                }
            }
        }
    }

}