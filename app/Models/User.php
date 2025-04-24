<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Enums\AuthProvider;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'picture',
        'role',
        'auth_provider',
        'email_otp',
        'email_otp_expires_at',
        'email_verified',
        'email_verified_at',
        'set_password_at'
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
            'set_password_at' => 'datetime',
            'password' => 'hashed',
            'email_verified' => 'boolean'
        ];
    }


    public function isGoogleUser(): bool
    {
        return $this->auth_provider === AuthProvider::GOOGLE;
    }

    public function isEmailUser(): bool
    {
        return $this->auth_provider === AuthProvider::EMAIL;
    }

}
