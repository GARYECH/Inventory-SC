<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone_number', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Relationship: A user can have many orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

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

    /**
     * 🌟 JALAN NINJA: MEMBELOKKAN TUJUAN EMAIL NOTIFIKASI 🌟
     * Fitur bawaan Laravel untuk memanipulasi alamat tujuan email.
     */
    public function routeNotificationForMail($notification)
    {
        // 1. Kalau Admin, kirim ke email SC dan email UC pribadimu sekaligus!
        if ($this->role === 'admin') {
            return [
                'studentcouncil@ciputra.ac.id',
                'gchristian02@student.ciputra.ac.id'
            ];
        }

        // 2. Kalau Mahasiswa, kirim ke email aslinya
        return $this->email;
    }
}