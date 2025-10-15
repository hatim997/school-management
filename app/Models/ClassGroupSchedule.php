<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassGroupSchedule extends Model
{
    use HasFactory;

    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }
}
