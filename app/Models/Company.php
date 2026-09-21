<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public function users()
    {
	return $this->belongsToMany(User::class);
    }

    public function jobs()
    {
	return $this->hasMany(Job::class);
    }

}
