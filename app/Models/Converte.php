<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Converte extends Model
{
    use HasFactory;
    protected $fillable = ['date', 'client_username', 'convertedSymbol', 'amount',"commentaire","devise"];

    // Define the relationship with the Client model
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Define the relationship with the Device model
    public function device()
    {
        return $this->belongsTo(Devise::class);
    }
}
