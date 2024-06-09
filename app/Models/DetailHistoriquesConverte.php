<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailHistoriquesConverte extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'client_username',
        'convertedSymbol',
        'amount',
        'commentaire',
        'devise',
        'id_historique'
    ];
}
