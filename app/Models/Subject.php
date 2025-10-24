<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    public function childSubjects()
    {
        return $this->hasMany(ChildSubject::class);
    }

    public function classGroups()
    {
        return $this->hasMany(ClassGroup::class);
    }
}
