<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassGroupStudent extends Model
{
    use HasFactory;

    public function parentChild()
    {
        return $this->belongsTo(ParentChild::class, 'parent_child_id');
    }

    public function classGroup()
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }
}
