<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
	protected $fillable = ['name'];	
    public function users()
    {
        return $this->belongsTo('App\Users');
    }

    public function Passwords()
    {
        return $this->hasMany('App\Passwords');
    }
}
