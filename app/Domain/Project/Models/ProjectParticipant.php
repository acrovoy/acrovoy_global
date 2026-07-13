<?php

namespace App\Domain\Project\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Project\Enums\ProjectParticipantStatus;

class ProjectParticipant extends Model
{
    protected $fillable = [
        'project_id',

        'participant_type',
        'participant_id',

        'status',

        'invited_at',
        'viewed_at',
        'accepted_at',
        'declined_at',
    ];

    protected $casts = [

    


        'status' => ProjectParticipantStatus::class,

        'invited_at'  => 'datetime',
        'viewed_at'   => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

    /**
     * Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Supplier / Company / Future entities
     */
    public function participant()
    {
        return $this->morphTo();
    }
}