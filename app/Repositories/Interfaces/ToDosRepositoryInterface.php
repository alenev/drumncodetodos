<?php

namespace App\Repositories\Interfaces;

use App\Models\ToDos;
interface ToDosRepositoryInterface
{
    public function all();
    public function getPaginated(int $perPage);
    public function create(array $data);
    public function update(array $data, $id);
    public function delete($id);
    public function find($id);
    
}