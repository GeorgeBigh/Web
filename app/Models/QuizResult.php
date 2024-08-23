<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    use HasFactory;
    


    protected $fillable = ['user_id', 'quiz_id', 'score', 'video_stream'];
}
