<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    use HasFactory;
    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $fillable = ['username', 'nom', 'localisation', 'commentaire', 'password', 'bloque'];
}
