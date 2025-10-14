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
}
