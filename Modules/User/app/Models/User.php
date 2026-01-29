<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Modules\Post\Models\Fav;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use LogsActivity;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'avatar',
        'birthdate',
        'gender',
        'account_type',
        'company_name',
        'license_number',
        'address',
        'office_request_status',
        'office_rejection_reason',
        'otp_code',
        'is_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birthdate' => 'date',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->dontSubmitEmptyLogs();
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        // Already a full URL (external avatar like Google/Facebook)
        if (Str::startsWith($this->avatar, ['http://', 'https://'])) {
            return $this->avatar;
        }

        // Return full storage URL for local files
        return Storage::disk('public')->url($this->avatar);
    }

    /**
     * Get all posts by this user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(\Modules\Post\Models\Post::class);
    }

    /**
     * Get all orders by this user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(\Modules\Order\Models\Order::class);
    }

    /**
     * Get all addresses for this user.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(\Modules\Shipping\Models\UserAddress::class);
    }

    public function favourites() {
        return $this->hasMany(Fav::class,'user_id');
    }
}

