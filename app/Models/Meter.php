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
        'customer_id',
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

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($meter) {
            $meter->code = static::generateUniqueCode();
        });
    }

    protected static function generateUniqueCode(): string
    {
        do {
            $number = mt_rand(0, 99999999999);
            $code = 'MTR-'.str_pad($number, 11, '0', STR_PAD_LEFT);
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
