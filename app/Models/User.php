<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'wp_creator_id',
        'is_active',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Helper methods pour vérifier les rôles
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCreator(): bool
    {
        return $this->role === 'creator';
    }

    public function isLogistic(): bool
    {
        return $this->role === 'logistic';
    }

    public function creator()
    {
        return $this->hasOne(Creator::class);
    }

    protected $attributes = [
        'role' => 'user',
        'is_active' => true,
    ];

    // Vérifie si l'utilisateur a un rôle spécifique
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    // Vérifie si l'utilisateur a l'un des rôles spécifiés
    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }

    // Scope pour les utilisateurs actifs
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope par rôle
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
