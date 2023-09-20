<?php

namespace App\Repositories\API\Interfaces;

use App\Models\ToDos;
interface ToDosRepositoryInterface
{
    public function all();
    public function getAll(array $params);
    public function create(array $data);
    public function update(array $data, $id);
    public function delete($id);
    public function find($id);
    public function getChilds($id);
    
}