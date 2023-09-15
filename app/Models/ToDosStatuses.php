<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToDosStatuses extends Model
{
    use HasFactory;

    protected $table = 'todos_statuses';

    protected $fillable = [
        'name'
    ];

    public function getAll()
    {
      return $this->get()->all();

    } 
}
