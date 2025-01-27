<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->is_activate == false) {
            Notification::make()
                ->title("Login Failed")
                ->body("User is not active")
                ->danger()
                ->send();
            return false;
        }

        if ($this->is_locked == true) {
            Notification::make()
                ->title("Login Failed")
                ->body("User is locked")
                ->danger()
                ->send();

            return false;
        }

        return true; //str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'fname',
        'mname',
        'lname',
        'number',
        'user_type',
        'is_locked',
        'profile_pic',
        'dob',
        'gender',
        'is_activate',
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
            'is_locked' => 'bool',
            'dob' => 'datetime',
            'is_activate' => 'bool',
        ];
    }
}
