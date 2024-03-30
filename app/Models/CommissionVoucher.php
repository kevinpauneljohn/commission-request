<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'category','request_id','voucher','user_id','approver','issuing_type','issuer'
        ,'transaction_reference_no','amount_transferred','is_approved','drive_link'
    ];

    public function setVoucherAttribute($value): void
    {
        $this->attributes['voucher'] = json_encode($value);
    }

    public function getVoucherAttribute($value)
    {
        return json_decode($value);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    //creator of the comm voucher
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class,'approver');
    }

    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
