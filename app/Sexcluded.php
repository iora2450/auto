<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sexcluded extends Model
{
    protected $fillable =[

        "reference_no", "estadodte",  "user_id", "warehouse_id", "total_qty", "total_discount", "excluded_id", "total_tax", "grand_total", "payment_method",
    ];

    public function excluded()
    {
        return $this->belongsTo('App\Excluded');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
