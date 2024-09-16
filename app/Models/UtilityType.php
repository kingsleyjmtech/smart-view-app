<?php

namespace App\Models;

use App\Traits\Traits\Shared\HasStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UtilityType extends Model
{
    use HasFactory;
    use HasStatus;
    use SoftDeletes;

    public const STATUS_SELECT = [
        'Active' => 'Active',
        'Inactive' => 'Inactive',
    ];

    public const ACTIVE_STATUS = 'Active';

    public const INACTIVE_STATUS = 'Inactive';

    public $table = 'utility_types';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected $casts = [

    ];

    protected array $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }
}
