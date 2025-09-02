<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'company_id',
        'slug',
        'name',
        'category_id',
        'description',
        'indications',
        'fda_pathway',
        'reimbursable',
        'margin_target',
        'status',
    ];

    protected $casts = [
        'reimbursable'  => 'bool',
        'margin_target' => 'float',
    ];

    public function company()    { return $this->belongsTo(Company::class, 'company_id'); }
    public function category()   { return $this->belongsTo(Category::class); }
    public function specialties(){ return $this->belongsToMany(Specialty::class, 'device_specialty'); }
    public function facilityTypes(){ return $this->belongsToMany(FacilityType::class, 'device_facility_type'); }
    public function territories(){ return $this->belongsToMany(Territory::class, 'device_territory'); }
    public function documents()  { return $this->hasMany(DeviceDocument::class); }
    public function clearance()  { return $this->hasOne(RegulatoryClearance::class); }
    public function reimbursementCodes(){ return $this->hasMany(ReimbursementCode::class); }
}
