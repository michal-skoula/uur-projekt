<?php

namespace App\Models;

use Database\Factories\AnalyticsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Analytics extends Model
{
    /** @use HasFactory<AnalyticsFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'subject_type',
        'subject_id',
        'url',
        'visitor_hash',
        'referrer',
        'device_type',
        'country',
    ];

    /** @return MorphTo<Model, $this> */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
