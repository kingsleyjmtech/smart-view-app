<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MeterReading extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    public $table = 'meter_readings';

    protected $fillable = [
        'meter_id',
        'reading_date',
        'value',
        'source',
    ];

    protected $casts = [

    ];

    protected array $dates = [
        'reading_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }
}
