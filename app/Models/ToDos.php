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
        'id_parent_todo',
        'id_status',
        'priority',
        'title',
        'description',
        'completed_at'
    ];

}
