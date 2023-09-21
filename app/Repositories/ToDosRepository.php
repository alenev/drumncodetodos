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
    $childsTree = ToDosHelper::buildParentChildTree($dbToDos->toArray());
    return $childsTree;

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