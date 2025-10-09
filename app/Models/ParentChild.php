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
}
