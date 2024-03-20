<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutomationTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['automation_id','title','description','assigned_to_role','creator','days_before_due_date','sequence_id'];

    public function automation()
    {
        return $this->belongsTo(Automation::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class,'creator');
    }
}
