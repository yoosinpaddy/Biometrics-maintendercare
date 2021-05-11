<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffFaceRecord extends Model
{
    use HasFactory,SoftDeletes;
    public function staff()
    {
        return $this->belongsTo(Staff::class,'reg_no','staff_id');
    }
}
