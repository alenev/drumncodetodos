<?php

namespace App\Repositories;

use App\Models\ToDos;
use App\Repositories\API\Interfaces\ToDosRepositoryInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToDosRepository implements ToDosRepositoryInterface
{
    private int $perPage;
    private object $search;

    public function all()
    {
      return ToDos::get()->all();

    } 
    public function getPaginated(array $params)
    {
  
       if(!empty($params["per_page"])){
          $this->perPage = $params["per_page"];
       }else if(!empty(env('TODOS_PER_PAGE'))){
          $this->perPage = env('TODOS_PER_PAGE');
       }else{
          $this->perPage = 10;
       }

       $this->search = ToDos::query();

       $this->search->where('id_user', $params["id_user"]);

       if(array_key_exists('search_keywords', $params) && !empty($params["search_keywords"])){
        $this->search
        ->whereFullText(['title', 'description'], ''.$params["search_keywords".''], ['mode' == 'boolean']);
      }

       if(array_key_exists('priority', $params) && !empty($params["priority"])){
        $params["priority"] = explode(",",$params["priority"]);
        $this->search->whereBetween('priority', [$params["priority"][0], $params["priority"][1]]);
       }

       if(array_key_exists('id_status', $params) && !empty($params["id_status"]) && $params["id_status"] > 0){
         $this->search->where('id_status', '=', $params['id_status']);
       }

      return $this->search
      ->orderBy('id')
      ->paginate($this->perPage);

    }
    public function create(array $data)
    {
       return ToDos::create($data);
    }
    public function update(array $data, $id) 
    {
        $update = ToDos::where('id', $id)->update($data);
        if($update){
          return $this->find($id);
        }
    }
    public function delete($id) {
        return ToDos::destroy($id);
    }
    public function find($id){
        return ToDos::where('id', $id)->get();
    }

    public function getChilds($id)
    {
        $toDos = new ToDos;
        return $toDos->childs($id);
    }

    
}