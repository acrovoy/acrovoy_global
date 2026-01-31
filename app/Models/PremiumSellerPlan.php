<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremiumSellerPlan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'popular'];

    // Связь с PlanFeature
    public function planFeatures()
    {
        return $this->hasMany(PlanFeature::class, 'plan_id');
    }
}