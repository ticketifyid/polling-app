<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    public $timestamps = false;

    protected $fillable = ['nama', 'candidate_id'];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function booted(): void
    {
        static::creating(function (Vote $vote) {
            $vote->created_at = now();
        });
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
