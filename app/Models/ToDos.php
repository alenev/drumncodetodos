<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToDos extends Model
{
    use HasFactory;

    protected $table = 'todos';
    protected $fillable = [
        'id_user',
        'id_paarent_todo',
        'id_status',
        'priority',
        'title'
    ];

    public static function add($fields):bool|object
    {

        $city = new static;
        $city->fill($fields);
        $city->save();

     }

     return $city;

}
}
