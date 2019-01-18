<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class spots extends Model
{
    public function users()
    {
    	return $this->belongsTo('App\Users');
    }
}
