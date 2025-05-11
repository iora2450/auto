<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    protected $fillable =[

        "code", "name", "state_id",
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
