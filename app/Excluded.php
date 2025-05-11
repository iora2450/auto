<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Excluded extends Model
{
    protected $fillable =[

        "name", "dui", "is_active", "address", "phone", "email", "user_id", "state_id", "municipality_id", "is_active"
    ];

    public function estado()
    {
        return $this->belongsTo('App\State', 'state_id', 'id');
        
    }

    public function municipio()
    {
        return $this->belongsTo('App\Municipality', 'municipality_id','id');
        
    }
}
