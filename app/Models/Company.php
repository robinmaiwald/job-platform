<?php

namespace App\Models;

use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'website'])]
class Company extends Model
{

    use HasFactory;

    public function users()
    {
	    return $this->belongsToMany(User::class);
    }

    public function jobs()
    {
	    return $this->hasMany(Job::class);
    }

    public function owner()
    {
	    return $this->belongsTo(User::class, 'owner_id');
    }
}
