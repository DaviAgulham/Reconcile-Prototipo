<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Payment extends Model
{
    protected $dates = [   
        'created_at',
        'notified_at',
        'confirmed_at',
        'rollback_confirmed_at'
      ];
    
}
