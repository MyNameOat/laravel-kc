<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuAccess extends Model
{
    protected $fillable = ['menu_id', 'department_id', 'user_id', 'actor_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
