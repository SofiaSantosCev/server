<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Passwords extends Model
{
	protected $fillable = ['title'];
	public function categories()
    {
        return $this->belongsTo('App\Category');
    }

    public function users()
    {
        return $this->belongsTo('App\Users');
    }
}

