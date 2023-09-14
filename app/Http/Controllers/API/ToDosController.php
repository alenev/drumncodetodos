<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ToDosController extends Controller
{
    public function createToDo(Request $request):JsonResponse 
    {
        
        return Controller::apiResponceSuccess('test', 200);
    }
}
