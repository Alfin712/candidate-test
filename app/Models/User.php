<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
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
        'role',
    ];

    public const ROLE_ADMIN = 'admin';
    public const ROLE_STAFF = 'staff';
    public const ROLE_VIEWER = 'viewer';
    // Legacy alias retained for backwards compatibility with older tests/fixtures.
    public const ROLE_ENGINEER = 'engineer';

    public static function roles(): array
    {
        return [self::ROLE_ADMIN, self::ROLE_STAFF, self::ROLE_VIEWER];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isStaff(): bool
    {
        // Treat legacy 'engineer' as staff for backwards compatibility.
        return in_array($this->role, [self::ROLE_STAFF, self::ROLE_ENGINEER], true);
    }

    public function isViewer(): bool
    {
        return $this->role === self::ROLE_VIEWER;
    }

    /**
     * Whether the user can mutate domain data (create/edit/delete suppliers & layups).
     */
    public function canManage(): bool
    {
        return $this->isAdmin() || $this->isStaff();
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $p) {
            if ($p !== '') {
                $initials .= mb_strtoupper(mb_substr($p, 0, 1));
            }
        }
        return $initials !== '' ? $initials : mb_strtoupper(mb_substr((string) $this->email, 0, 2));
    }

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
}
