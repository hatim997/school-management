<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildSubject extends Model
{
    use HasFactory;

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function parentChild()
    {
        return $this->belongsTo(ParentChild::class, 'parent_child_id');
    }

    public function classGroups()
    {
        return $this->hasManyThrough(
            ClassGroup::class,
            ClassGroupStudent::class,
            'parent_child_id',    // Foreign key on class_group_students
            'id',                 // Foreign key on class_groups
            'parent_child_id',    // Local key on child_subjects
            'class_group_id'      // Local key on class_group_students
        );
    }
}
