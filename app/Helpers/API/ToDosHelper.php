<?php

namespace App\Helpers\API;

use App\Repositories\ToDosRepository;
use App\Models\ToDosStatuses;

class ToDosHelper
{

    public static function requestValidationErrorsData(object $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {

            $validationErrors = $request->validator->errors()->messages();

            $validationErrorsFirst = current((array) $validationErrors);

            return $validationErrorsFirst[0];

        } else {

            return false;

        }
    }

    public static function getRequestData(object $request)
    {
        if (str_contains($request->header('Content-Type'), 'text/plain')) {
            return $request->json()->all();
        } else {
            return $request->all();
        }
    }

    public static function checkExistParentTodo(object $request, object $db)
    {
        $requestData = self::getRequestData($request);
        if (intval($requestData['id_parent_todo']) == 0) {
            return true;
        }
        $parentTodo = $db->find($requestData['id_parent_todo'])->first();
        if (
            !empty($requestData['id_parent_todo']) &&
            empty($parentTodo)
        ) {
            return false;
        }
        if (
            !empty($requestData['id_parent_todo']) &&
            !empty($parentTodo) &&
            $requestData['id_user'] != $parentTodo['id_user']
        ) {
            return false;
        }
        return true;
    }

    public static function checkTodoOwner(object $request, object $db)
    {
        $requestData = self::getRequestData($request);
        $todo = $db->find($requestData['id'])->first();
        if (intval($todo["id_user"]) != intval($requestData['id_user'])) {
            return false;
        }
        return true;
    }

    public static function checkToDoDeleteDisable(object $request, object $db){
        $requestData = self::getRequestData($request);
        $todo = $db->find($requestData['id'])->first();
        if (intval($todo["id_user"]) != intval($requestData['id_user'])) {
           return 'this todo has another owner';
        }
        $statusNameData = $requestData;
        $statusNameData["id_status"] = $todo["id_status"];
        $statusName = self::getStatusName($statusNameData, $db);
        if ($statusName == 'done') {
            return 'this todo has complete status';
        }
        return false;
    }

    public static function getStatusName(array $requestData, object $db)
    {
        $toDosStatuses = new ToDosStatuses;
        $status = $toDosStatuses->find($requestData["id_status"])->first();
        if ($status) {
            return $status["name"];
        } else {
            return false;
        }
    }

    public static function ChildTreeInsert(array $tree, array $child)
    {
        $insert = false;
        foreach ($tree as $key => &$node) {
            $insertNode = false;
            if ($node["id"] === $child["id_parent_todo"]) {
                $node["childs"][] = $child;
                $insert = true;
                return $tree;
            }
            if (isset($node["childs"]) && is_array($node["childs"]) && !empty($node["childs"])) {
                $sub = self::ChildTreeInsert($node["childs"], $child);
                if ($sub) {
                    $node["childs"] = $sub;
                    return $tree;
                }
            }

        }

        return false;
    }

    public static function buildParentChildTree(array $dataset)
    {
        $tree = [];
        foreach ($dataset as $key => $node) {
            $node['childs'] = [];
            if (empty($node['id_parent_todo']) || intval($node['id_parent_todo']) < 1) {
                $tree[] = $node;
            } else {
                $tree = self::ChildTreeInsert($tree, $node);
            }
        }
        return $tree;
    }

    public static function sortingChildsTree(array $childsTree, string $sort_field, string $sort_order){
        $key_values = array_column($childsTree, $sort_field); 
        $sortD = SORT_ASC;
        if($sort_order == 'desc'){
            $sortD = SORT_DESC;
        }
        array_multisort($key_values, $sortD, $childsTree);
        return $childsTree;
    }

    public static function checkUpdatePossibility(array $request, object $db)
    {
        $todos = $db->getAll(
            array(
                "id_user" => $request["id_user"]
            )
        );
        $undonesChildTodos = self::checkChildsStatuses($todos, $request["id"], $request["id_status"]);
        return $undonesChildTodos;
    }

    public static function checkChildsStatus(array $item, int $id_status)
    {
        if ($item["id_status"] != $id_status) {
            return false;
        } else {
            return true;
        }
    }



    public static function checkChildsStatuses(array $items, int $item_id, int $id_status, string $event = '', array $nodones = [])
    {
        foreach ($items as &$item) {
            if ($event == '') {
                if ($item["id"] === $item_id) {
                    if (isset($item["childs"]) && !empty($item["childs"])) {
                        return self::checkChildsStatuses($item["childs"], $item_id, $id_status, 'checkStatuses', $nodones);
                    } else {
                        return [];
                    }
                }
            }
            if ($event == 'checkStatuses') {

                if (!self::checkChildsStatus($item, $id_status)) {
                    $tmpItem = $item;
                    unset($tmpItem['childs']);
                    $nodones[] = $tmpItem;
                }
            }

            if (isset($item["childs"]) && !empty($item["childs"])) {
                $nodones = self::checkChildsStatuses($item["childs"], $item_id, $id_status, $event, $nodones);
            }
        }
        return $nodones;
    }

}