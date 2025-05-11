<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable =[

        "code", "name", "is_active"
    ];

    public function munipalities()
    {
        return $this->hasMany(Municipality::class);
    }
}
