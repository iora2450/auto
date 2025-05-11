<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSexcluded extends Model
{
    protected $table = 'product_sexcludeds';

    protected $fillable =[
        "sexcluded_id", "product_id", "qty", "purchase_unit_id", "net_unit_cost", "discount", "tax_rate", "tax", "total", "description",
    ];
}
