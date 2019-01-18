<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class users extends Model
{
    public function spots()
    {
    	return $this->hasMany('App\Spots');
    }

    public function roles()
    {
    	return $this->belongsTo('App\Roles');
    }

}
