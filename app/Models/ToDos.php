<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function parent()
    {
        return $this->belongsTo(ToDos::class, 'id_parent_todo');
    }

    public function children()
    {
        return $this->hasMany(ToDos::class, 'id_parent_todo');
    }

    public function descendants()
    {
        return $this->children()->with('descendants');
    }


    public function childs($id)
    {
        $todo = ToDos::find($id);
        if($todo){
        $childs = $todo->children;
        return $todo->descendants;
        }else{
            return 0;
        }
    }

}
