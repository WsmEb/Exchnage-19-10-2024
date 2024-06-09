<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depose extends Model
{
    use HasFactory;

    protected $fillable = ['date_depose','client','devise','amount','commentaire',"type"];

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
