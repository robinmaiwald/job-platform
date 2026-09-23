<?php

namespace App\Models;

use Database\Factories\JobFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['company_id', 'user_id', 'title', 'description', 'location'])]
class Job extends Model
{
    /** @use HasFactory<JobFactory> */
    use HasFactory;

    public function company()
    {
	return $this->belongsTo(Company::class);
    }

    public function user()
    {
	return $this->belongsTo(User::class);
    }
}
