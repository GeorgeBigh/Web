<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    
    protected $fillable = ['title', 'description', 'quiz_time', 'name']; // Include 'name' if it's part of the fillable properties

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

