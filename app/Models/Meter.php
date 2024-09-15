<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meter extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_SELECT = [
        'Active' => 'Active',
        'Inactive' => 'Inactive',
    ];

    public $table = 'meters';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'utility_type_id',
        'code',
        'location',
        'installation_date',
        'status',
    ];

    protected $casts = [

    ];

    protected array $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function utilityType(): BelongsTo
    {
        return $this->belongsTo(UtilityType::class);
    }

    public function meterReadings(): HasMany
    {
        return $this->hasMany(MeterReading::class);
    }

    public function meterTariffs(): HasMany
    {
        return $this->hasMany(MeterTariff::class);
    }

    public function consumptions(): HasMany
    {
        return $this->hasMany(Consumption::class);
    }
}
