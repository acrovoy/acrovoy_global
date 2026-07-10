<?php

namespace App\Domain\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Domain\RFQ\Models\Rfq;
use App\Models\User;
use App\Domain\Project\Enums\ProjectStatus;
use App\Domain\Project\Enums\ProjectVisibilityType;

class Project extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'buyer_id',
        'buyer_type',
        'created_by',
        'title',
        'description',
        'status',
        'visibility_type',
        'published_at',
        'closed_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'closed_at'    => 'datetime',
        'status' => ProjectStatus::class,
        'visibility_type' => ProjectVisibilityType::class,
    ];

    /**
     * Владелец проекта (Buyer).
     */
    public function buyer()
    {
        return $this->morphTo();
    }

    /**
     * Пользователь, создавший проект.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * RFQ, входящие в проект.
     */
    public function rfqs()
    {
        return $this->hasMany(Rfq::class);
    }

    public function getPublicIdAttribute()
{
    return 'PROJECT-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
}
}