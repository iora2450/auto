<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class quedanxsale extends Model
{
        protected $fillable =[

        "id_quedan", "id_sale"
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
