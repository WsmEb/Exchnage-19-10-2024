<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailHistoriquesOperation extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'percentage',
        'total'
        ,'quantity',
        'type_operation',
        'ville',
        'prix',
        'id_historique'
];
}
