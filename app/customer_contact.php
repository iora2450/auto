<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class customer_contact extends Model
{
     protected $fillable =[
        "customer_id", "contact_num", "description","email"
    ];
}
