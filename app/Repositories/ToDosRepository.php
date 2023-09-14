<?php

namespace App\Repositories;

use App\Models\ToDos;
use App\Repositories\Interfaces\ToDosRepositoryInterface;
class ToDosRepository implements ToDosRepositoryInterface
{
    public function all()
    {
      return ToDos::get()->all();

    } 
    public function getPaginated(int $perPage)
    {
        return ToDos::paginate($perPage);
    }
    public function create(array $data)
    {
       return ToDos::create($data);
    }
    public function update(array $data, $id) 
    {
        return ToDos::where('id', $id)->update($data);
    }
    public function delete($id) {
        return ToDos::destroy($id);
    }
    public function find($id){
        return ToDos::where('id', $id)->get();
    }
}