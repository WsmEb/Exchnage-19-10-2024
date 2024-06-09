<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfert extends Model
{
    use HasFactory;
    protected $fillable = ['date', 'client', 'expediteur', 'recepteur', 'devise', 'solde'];

    public function info_expediteur()
    {
        return $this->belongsTo(Client::class, 'expediteur');
    }

    public function info_recepteur()
    {
        return $this->belongsTo(Client::class, 'recepteur');
    }
}
