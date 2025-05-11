<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Retention extends Model
{
    protected $fillable =[
        "reference_no", "invoice", "user_id", "supplier_id", "estadodte", "total", "warehouse_id", "document_id", "resolucion", "serie", "numerocontrol", 
    ];

    public function retentionxpurchase()
    {
        return $this->hasMany('App\RetentionxPurchase');
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
