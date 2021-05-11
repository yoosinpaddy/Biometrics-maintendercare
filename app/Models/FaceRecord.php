<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaceRecord extends Model
{
    use HasFactory,SoftDeletes;

    public function student()
    {
        return $this->belongsTo(Student::class,'upi_no','upi_no');
    }
}
