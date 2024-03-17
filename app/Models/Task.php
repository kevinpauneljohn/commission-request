<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'assigned_to',
        'creator',
        'status',
        'due_date',
        'time',
        'request_id'
    ];

    protected $appends = ['formatted_request_id'];

    public function assignedTo()
    {
        return $this->belongsTo(User::class,'assigned_to');
    }

    public function author()
    {
        return $this->belongsTo(User::class,'creator');
    }

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function getFormattedRequestIdAttribute()
    {
        return str_pad($this->request_id, 5, '0', STR_PAD_LEFT);
    }

    public function actionTakens()
    {
        return $this->hasMany(ActionTaken::class);
    }
}
