<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClassGroup extends Model
{
    use HasFactory;

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function schedules()
    {
        return $this->hasMany(ClassGroupSchedule::class);
    }

    public function classGroupStudents()
    {
        return $this->hasMany(ClassGroupStudent::class, 'class_group_id');
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class, 'class_group_id');
    }

    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            ClassGroupStudent::class,
            'class_group_id',           // Foreign key on class_group_students table
            'id',                       // Local key on users table
            'id',                       // Local key on class_groups table
            'parent_child_id'           // Foreign key on class_group_students table
        )
        ->join('parent_children', 'parent_children.id', '=', 'class_group_students.parent_child_id')
        ->where('users.id', '=', DB::raw('parent_children.child_id'));
    }
}
