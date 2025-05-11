<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuedanPurchase extends Model
{
    protected $fillable =[
        "reference_no", "date_quedan", "number_invoice", "supplier_id", "status", "total", "due_date", "warehouse_id"
    ];

    public function quedanxpurchase()
    {
        return $this->hasMany('App\Quedanxpurchase');
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
