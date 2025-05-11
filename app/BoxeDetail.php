<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BoxeDetail extends Model
{
    protected $table = 'boxe_details';
    
    protected $fillable =[
        "boxe_id", "ticket_id", "qty_producto", "total_dinero"
    ];
}
