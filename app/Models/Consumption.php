<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consumption extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const AGGREGATION_PERIOD_SELECT = [
        'Hourly' => 'Hourly',
        'Daily' => 'Daily',
        'Weekly' => 'Weekly',
        'Monthly' => 'Monthly',
        'Quarterly' => 'Quarterly',
        'Yearly' => 'Yearly'
    ];
    
    public $table = 'consumptions';
    
    protected $fillable = [
        'meter_id',
        'aggregation_period',
        'value',
        'date'
    ];
    
    protected $casts = [
        
    ];
    
    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];    
    
    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }
}
