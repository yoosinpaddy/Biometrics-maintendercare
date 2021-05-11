<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use HasFactory,SoftDeletes;


    public function students()
    {
        return $this->belongsTo(Student::class,'student_id');
    }
}
