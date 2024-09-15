<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tariff extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = 'tariffs';

    protected $fillable = [
        'rate',
        'description',
        'start_date',
        'end_date',
        'name',
    ];

    protected $casts = [

    ];

    protected array $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function meterTariffs(): HasMany
    {
        return $this->hasMany(MeterTariff::class);
    }
}
