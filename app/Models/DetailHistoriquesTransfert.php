<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailHistoriquesTransfert extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'expediteur',
        'recepteur',
        'solde',
        'id_historique'
    ];
}
