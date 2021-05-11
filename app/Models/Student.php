<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory,SoftDeletes;

    public function guardians()
    {
        return $this->hasMany(Guardian::class);
    }

    public function getStream()
    {
        return $this->hasOne(Stream::class,'id','stream');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
