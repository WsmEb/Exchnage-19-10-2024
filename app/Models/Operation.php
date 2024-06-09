<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;
    protected $fillable = ['comments','date','client','percentage','total','quantity','devise','type_operation','ville','prix']; 
    
}
