<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer',
        'project',
        'model_unit',
        'phase',
        'block',
        'lot',
        'total_contract_price',
        'financing',
        'request_type',
        'sd_rate',
        'cheque_number',
        'bank_name',
        'cheque_amount',
        'message',
        'message',
        'user_id',
        'backend_user',
        'parent_request_id',
        'status'
    ];

    /*Note: a request task automation is fired once a request was created found in
    EventServiceProviders and RequestObserver*/

    protected $appends = ['colored_status','formatted_id','child_requests','parent_request'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setBuyerAttribute($value): void
    {
        $this->attributes['buyer'] = json_encode($value);
    }

    public function getBuyerAttribute($value)
    {
        return json_decode($value);
    }

    public function getColoredStatusAttribute(): string
    {
        return match ($this->status) {
            "pending" => '<span class="badge badge-warning">'.$this->status.'</span>',
            "declined" => '<span class="badge badge-danger">'.$this->status.'</span>',
            "delivered" => '<span class="badge badge-primary">'.$this->status.'</span>',
            "on-going" => '<span class="badge bg-purple">'.$this->status.'</span>',
            "completed" => '<span class="badge badge-success">'.$this->status.'</span>',
            default => "",
        };
    }

    public function getFormattedIdAttribute(): string
    {
        $id = str_pad($this->id, 5, '0', STR_PAD_LEFT);
        return match ($this->request_type) {
            "cheque_pickup" => 'RQ-PUP-' . $id,
            "commission_request" => 'RQ-COM-' . $id,
            default => "",
        };
    }

    public function getChildRequestsAttribute()
    {
        $requests = collect(Request::where('parent_request_id',$this->id)->get())->pluck('formatted_id');
        $requestIds = '';
        foreach ($requests as $id){
            $requestIds .= '<span class="text-info mr-1">'.$id.'</span>';
        }
        return $requestIds;
    }

    public function getParentRequestAttribute(): string
    {

        if(is_null($this->parent_request_id))
        {
            return "";
        }
        $id = str_pad($this->parent_request_id, 5, '0', STR_PAD_LEFT);
        return match ($this->request_type) {
            "cheque_pickup" => 'RQ-PUP-' . $id,
            "commission_request" => 'RQ-COM-' . $id,
            default => "",
        };
    }


    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function findings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Finding::class);
    }
}
