<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
     protected $fillable = ['quiz_id', 'question_text', 'point'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function choices()
    {
        return $this->hasMany(Choice::class);
    
    }




    public function company()
    {
        return $this->belongsTo(Company::class); // Adjust based on your actual relationship
    }


}
