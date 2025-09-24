<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // manejo simple de roles sin Spatie
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // Relación: usuario tiene muchas reservas
    public function reservas()
    {
        return $this->hasMany(\App\Models\Reserva::class);
    }

    /**
     * isAdmin: usa la columna 'role' si existe.
     */
    public function isAdmin(): bool
    {
        if (array_key_exists('role', $this->attributes ?? []) || isset($this->role)) {
            return $this->role === 'admin';
        }

        return false;
    }

    /**
     * hasRole fallback simple.
     *
     * Permite que llamadas a $user->hasRole('admin') funcionen aunque no uses Spatie.
     * Acepta string o array.
     */
    public function hasRole($roles): bool
    {
        if (! (array_key_exists('role', $this->attributes ?? []) || isset($this->role)) ) {
            return false;
        }

        $current = $this->role;

        if (is_array($roles)) {
            return in_array($current, $roles, true);
        }

        return $current === (string) $roles;
    }

    /**
     * assignRole fallback simple.
     * Si alguien llama $user->assignRole('admin') lo guarda en la columna role.
     */
    public function assignRole($role)
    {
        if (is_array($role)) {
            $role = $role[0] ?? null;
        }

        if ($role === null) {
            return $this;
        }

        $this->role = (string) $role;
        $this->save();

        return $this;
    }

    /**
     * removeRole fallback (quita role).
     */
    public function removeRole($role = null)
    {
        // Si pasan un role concreto, solo lo borra si coincide; si no pasan nada lo borra siempre.
        if ($role === null || $this->role === $role) {
            $this->role = null;
            $this->save();
        }

        return $this;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }
}
