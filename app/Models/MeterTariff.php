<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeterTariff extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    public $table = 'meter_tariffs';
    
    protected $fillable = [
        'meter_id',
        'tariff_id',
        'effective_from',
        'effective_to'
    ];
    
    protected $casts = [
        
    ];
    
    protected $dates = [
        'effective_from',
        'effective_to',
        'created_at',
        'updated_at',
        'deleted_at'
    ];    
    
    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }
}
