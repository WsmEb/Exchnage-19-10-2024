<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devise extends Model
{
    use HasFactory;
    protected $primaryKey = 'symbol';
    
    public $incrementing = false;
    protected $fillable = ['symbol','description','base'];  
}
