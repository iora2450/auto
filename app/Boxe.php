<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Boxe extends Model
{
    protected $fillable =[

        "user_id", "reference_no", "warehouse_id", "cash_register_id", "note"
    ];

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse');
    }
}
