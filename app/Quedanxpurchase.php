<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quedanxpurchase extends Model
{
   protected $table = 'quedanxpurchases';

    protected $fillable =[
        "quedan_id", "purchase_id", "return_id"
    ];

    public function Quedanpurchase()
    {
        return $this->belongsTo('App\Quedanpurchase');
    }

    public function purchases()
    {
        return $this->belongsTo('App\Purchase');
    }

    public function return_purchases()
    {
        return $this->belongsTo('App\ReturnPurchase');
    }    

    public function supplier()
    {
        return $this->belongsTo('App\Supplier');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse');
    }
}
