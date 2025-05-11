<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetentionxPurchases extends Model
{
    protected $table = 'retentionx_purchases';

    protected $fillable =[
        "retention_id", "purchase_id",
    ];

    public function retententionpurchase()
    {
        return $this->belongsTo('App\Retention');
    }

    public function purchases()
    {
        return $this->belongsTo('App\Purchase');
    }
}
