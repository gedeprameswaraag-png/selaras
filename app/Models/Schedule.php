<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'start_time',
        'end_time',
        'type',
        'is_fixed',
        'description',

        'recurrence_id',
        'is_recurring',
        'recurrence_type',
        'recurrence_start_date',
        'recurrence_end_date',
        'recurrence_days',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_fixed' => 'boolean',
        'is_recurring' => 'boolean',
        'recurrence_start_date' => 'date',
        'recurrence_end_date' => 'date',
        'recurrence_days' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
