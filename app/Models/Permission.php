<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    public $table = 'permissions';
    
    protected $fillable = [
        'name'
    ];
    
    protected $casts = [
        
    ];
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];    
    
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
