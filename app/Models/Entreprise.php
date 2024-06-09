<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    use HasFactory;

    protected $primaryKey = 'titre'; // Set the primary key
    public $incrementing = false; // Disable auto-incrementing for primary key

    protected $fillable = ['titre', 'description']; // Allow mass assignment for these fields
}
