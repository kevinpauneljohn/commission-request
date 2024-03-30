<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_voucher_id','label','amount'
    ];

    public function commission_voucher()
    {
        return $this->belongsTo(CommissionVoucher::class);
    }
}
