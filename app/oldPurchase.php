<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable =[

        "reference_no", "user_id", "warehouse_id", "supplier_id", "item", "total_qty", "total_discount", "total_tax", "total_cost", "order_tax_rate", "order_tax", "order_discount", "shipping_cost", "grand_total","paid_amount", "status", "payment_status", "document", "note","confirmation_date","dispatch_date","estimated_delivery_date", "date_received","deadline_delivery_date",
        "type_purchase","customer_id","serial","chemistry_application", "email_suppliers","terms", 'name_purchase', 'email_purchase','po_number', 'suppliers_name'
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
