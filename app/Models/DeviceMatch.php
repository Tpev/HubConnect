<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class DeviceMatch extends Model
{
    protected $fillable = [
        'device_id','manufacturer_id','distributor_id',
        'status','initiator','requested_territory_ids',
        'exclusivity','proposed_commission_percent','message',
        'approved_at','rejected_at',
    ];

    protected $casts = [
        'requested_territory_ids' => AsArrayObject::class,
        'exclusivity' => 'bool',
        'proposed_commission_percent' => 'float',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function device()       { return $this->belongsTo(Device::class); }
    public function manufacturer() { return $this->belongsTo(Company::class, 'manufacturer_id'); }
    public function distributor()  { return $this->belongsTo(Company::class, 'distributor_id'); }

    public function scopeForCompany($q, int $companyId) {
        return $q->where(function($w) use ($companyId) {
            $w->where('manufacturer_id', $companyId)
              ->orWhere('distributor_id', $companyId);
        });
    }
}
