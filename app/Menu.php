<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name'];

    public function menuAccesses()
    {
        return $this->hasMany(MenuAccess::class);
    }
}
