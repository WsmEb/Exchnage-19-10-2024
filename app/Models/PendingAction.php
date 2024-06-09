<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingAction extends Model
{
    use HasFactory;
    protected $fillable = ["comptable","action","page","status","details",'model'];
}
