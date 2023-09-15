<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\ToDosStatuses;

class ToDosStatusesController extends Controller
{
    private $toDosStatuses;
    private $db;

    public function getToDosStatuses(Request $request):JsonResponse
    {
        $this->db = new ToDosStatuses;
        $this->toDosStatuses = $this->db->getAll();
        if(!empty($this->toDosStatuses)){
            return Controller::ApiResponceSuccess([
                "message" => "todos statuses selected",
                "data" => $this->toDosStatuses
            ], 200);  
        }else{
            return Controller::ApiResponceError('todos statuses selected problem', 500); 
        } 
    }

}