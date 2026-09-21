<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'website'])]
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
