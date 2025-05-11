<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quedan extends Model
{
    protected $fillable =[

        "date_quedan", "number_invoice", "customer_id", "status", "total", "due_date"
    ];

    public function supplier()
    {
    	return $this->belongsTo('App\Supplier');
    }

    public function warehouse()
    {
    	return $this->belongsTo('App\Warehouse');
    }
}
