<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentChild extends Model
{
    use HasFactory;
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
    public function child()
    {
        return $this->belongsTo(User::class, 'child_id');
    }

    public function childSubjects()
    {
        return $this->hasMany(ChildSubject::class, 'child_subject_id');
    }

    public function classGroupStudents()
    {
        return $this->hasMany(ClassGroupStudent::class, 'class_group_student_id');
    }
}
