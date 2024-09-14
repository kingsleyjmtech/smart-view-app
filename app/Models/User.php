<?php

namespace App\Models;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    public const STATUS_SELECT = [
        'Active' => 'Active',
        'Inactive' => 'Inactive',
    ];

    public $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'timezone',
        'email_verified_at',
        'password',
        'remember_token',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [

    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function hasPermission(string $permissionName): bool
    {
        foreach ($this->roles as $role) {
            foreach ($role->permissions as $permission) {
                if ($permission->name == $permissionName) {
                    return true;
                }
            }
        }

        return false;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }

    public function sendEmailVerificationNotification(): void
    {
        $verifyUrl = config('app.frontend_url').'/auth/verify-email?'.http_build_query([
            'id' => $this->getKey(),
            'hash' => sha1($this->getEmailForVerification()),
        ]);

        $verifyEmail = new VerifyEmail;

        $verifyEmail->createUrlUsing(function () use ($verifyUrl) {
            return $verifyUrl;
        });

        $this->notify($verifyEmail);
    }

    public function sendPasswordResetNotification($token): void
    {
        $url = config('app.frontend_url').'/auth/reset-password?token='.$token.'&email='.urlencode($this->email);

        $resetPassword = new ResetPassword($token);
        $resetPassword->createUrlUsing(function () use ($url) {
            return $url;
        });

        $this->notify($resetPassword);
    }
}
