<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historiques_Operations extends Model
{
    use HasFactory;
    protected $table = 'historiques_operations'; 
    protected $fillable = ['id', 'datehistorique', 'commentaire', 'valeur', "client","devise"];

}
