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
        'status'
    ];

    /*Note: a request task automation is fired once a request was created found in
    EventServiceProviders and RequestObserver*/

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

    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function findings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Finding::class);
    }
}
