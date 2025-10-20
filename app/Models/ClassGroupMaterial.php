<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassGroupMaterial extends Model
{
    use HasFactory;

    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
