<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['company_id', 'title', 'description', 'location'])]
class Job extends Model
{
    public function company()
    {
	return $this->belongsTo(Company::class);
    }

    public function user()
    {
	return $this->belongsTo(User::class);
    }
}
