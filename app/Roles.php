<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class roles extends Model
{
    public function users()
    {
    	return $this->hasMany('App\Users');
    }
}
