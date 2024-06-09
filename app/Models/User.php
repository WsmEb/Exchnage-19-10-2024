<?php

namespace App\Models;
use App\Models\Client;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $fillable = [
        'username',
        'nom',
        'prenom',
        'password',
        'bloque',
        'role',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'permission_clients', 'utilisateur', 'client', 'username', 'username');
    }
    
}
