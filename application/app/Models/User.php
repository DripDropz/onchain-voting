<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Traits\HasHashIds;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\HashIdModel;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable,
        HasApiTokens,
        HasFactory,
        HasRoles,
        Notifiable,
        HasHashIds,
        HashIdModel;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'voter_id',
    ];

    protected $appends = [
        'hash',
        'user_roles',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function voting_power(): HasOne
    {
        return $this->hasOne(VotingPower::class);
    }

    public function userRoles(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->roles()->get()->toArray()
        );
    }
}
