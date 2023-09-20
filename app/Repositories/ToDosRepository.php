<?php

namespace App\Repositories;

use App\Models\ToDos;
use App\Repositories\API\Interfaces\ToDosRepositoryInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Helpers\API\ToDosHelper;

class ToDosRepository implements ToDosRepositoryInterface
{
  private object $search;
  public function all()
  {
    return ToDos::get()->all();

  }
  public function getAll(array $params)
  {

    $this->search = ToDos::query();

    $this->search->where('id_user', $params["id_user"]);

    if (array_key_exists('search_keywords', $params) && !empty($params["search_keywords"])) {
      $this->search
        ->whereFullText(['title', 'description'], '' . $params["search_keywords" . ''], ['mode' == 'boolean']);
    }

    if (array_key_exists('priority', $params) && !empty($params["priority"])) {
      $params["priority"] = explode(",", $params["priority"]);
      $this->search->whereBetween('priority', [$params["priority"][0], $params["priority"][1]]);
    }

    if (array_key_exists('id_status', $params) && !empty($params["id_status"]) && $params["id_status"] > 0) {
      $this->search->where('id_status', '=', $params['id_status']);
    }

    $dbToDos = $this->search->orderBy('id')->get();
    $ToDosHelper = new ToDosHelper;
    $childs = ToDosHelper::buildParentChildTree($dbToDos->toArray(), $ToDosHelper);
    return $childs;

    $result = [];
    $exclude = [];
    foreach ($dbToDos as $todo) {
      if (array_search($todo["id"], $exclude) === false) {
        $newExclude = [];
        $newChilds = [];
        $existChilds = [];
        if ($todo["id_parent_todo"] > 0) {
          $childs = $this->getChilds($todo["id"])->toArray();
          $parentKey = array_search($todo["id_parent_todo"], array_column($result, 'id'));
          if (!empty($childs)) {
            $childsData = ToDosHelper::toDosChilds($childs, $todo);
            $newExclude = $childsData["exclude"];
            $newChilds = $childsData["data"];

          } else {
            $newExclude[] = $todo["id"];
            $newChilds[] = $todo;
          }

          $exclude = array_merge($exclude, $newExclude);
          if (empty($result[$parentKey]["childs"])) {
            $result[$parentKey]["childs"] = $newChilds;
          } else {
            $existChilds[] = $result[$parentKey]["childs"];
            $newChilds = array_merge($existChilds, $newChilds);
            $result[$parentKey]["childs"] = $newChilds;
          }

        } else {
          $todo["childs"] = [];
          $result[] = $todo;
        }

      }
    }
    return $result;
  }
  public function create(array $data)
  {
    return ToDos::create($data);
  }
  public function update(array $data, $id)
  {
    $update = ToDos::where('id', $id)->update($data);
    if ($update) {
      return $this->find($id);
    }
  }
  public function delete($id)
  {
    return ToDos::destroy($id);
  }
  public function find($id)
  {
    return ToDos::where('id', $id)->get();
  }

  public function getChilds($id)
  {
    $toDos = new ToDos;
    return $toDos->childs($id);
  }


}