<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'role_id',
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
            'password' => 'hashed',
        ];
    }

    function role(){
        return $this->belongsTo(Role::class)->withDefault();
    }

    /** Resolved permission codes; not an attribute, so it is never persisted. */
    protected $cached_permissions = null;

    /** The site owner, who always holds every permission. */
    function isSuperAdmin(): bool {
        return $this->role_id && $this->role->slug === Role::SUPER_ADMIN;
    }

    /** Anyone with a role belongs to the staff and may open the dashboard. */
    function isStaff(): bool {
        return (bool) $this->role_id;
    }

    /**
     * Whether this user may do something, e.g. can('products.create').
     * The owner is never limited; everyone else is limited to the permissions
     * ticked on their role.
     */
    function hasPermission(string $code): bool {
        if (! $this->role_id) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->permissionCodes()->contains($code);
    }

    /**
     * Codes granted by this user's role, looked up once per request rather
     * than on every permission check.
     */
    function permissionCodes() {
        if ($this->cached_permissions === null) {
            $this->cached_permissions = $this->role_id
                ? $this->role->permissions()->pluck('code')
                : collect();
        }

        return $this->cached_permissions;
    }
    function image(){
        return $this->morphOne(Image::class,'imageable');
    }
    function reviews(){
        return $this->hasMany(Review::class);
    }
    function orders(){
        return $this->hasMany(Order::class);
    }
    function order_details(){
        return $this->hasMany(OrderDetail::class);
    }
    function payment(){
        return $this->hasMany(Payment::class)->withDefault();
    }
    function testimonials(){
        return $this->hasMany(Testimonial::class);
    }
}
