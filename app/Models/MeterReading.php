<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeterReading extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    public $table = 'meter_readings';
    
    protected $fillable = [
        'meter_id',
        'reading_date',
        'value'
    ];
    
    protected $casts = [
        
    ];
    
    protected $dates = [
        'reading_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];    
    
    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }
}
