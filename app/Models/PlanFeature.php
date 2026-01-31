<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanFeature extends Model
{
    use HasFactory;

    protected $fillable = ['plan_id', 'feature_id', 'value'];

    public function feature()
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    public function planFeatures()
    {
        return $this->belongsTo(PremiumSellerPlan::class, 'plan_id');
    }
}
