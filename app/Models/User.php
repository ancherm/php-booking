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
        'first_name',
        'last_name',
        'patronymic',
        'login',
        'password',
        'user_type',
        'disabled',
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
            'password' => 'hashed',
            'disabled' => 'boolean',
        ];
    }


    public function getFullNameAttribute(): string
    {
        $name = $this->last_name . ' ' . $this->first_name;
        if ($this->patronymic) {
            $name .= ' ' . $this->patronymic;
        }
        return $name;
    }


    public function hasRole(string $role): bool
    {
        return $this->user_type === $role;
    }

    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    public function isManager(): bool
    {
        return $this->user_type === 'manager';
    }

    public function isClient(): bool
    {
        return $this->user_type === 'client';
    }

 
    public function admin()
    {
        return $this->hasOne(Admin::class, 'id');
    }


    public function client()
    {
        return $this->hasOne(Client::class, 'id');
    }


    public function getAuthIdentifierName()
    {
        return 'login';
    }


    public function username()
    {
        return 'login';
    }
}
