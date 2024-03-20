<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Automation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title','user_id','is_active'];

    public function automationTasks()
    {
        return $this->hasMany(AutomationTask::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
