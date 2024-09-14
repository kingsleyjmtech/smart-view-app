<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UtilityType extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    public $table = 'utility_types';
    
    protected $fillable = [
        'name',
        'description'
    ];
    
    protected $casts = [
        
    ];
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];    
    
    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }
}
