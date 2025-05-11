<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable =[
        "customer_group_id", "user_id", "name", "company_name",
        "email", "phone_number", "tax_no", "address", "city",
        "state", "postal_code", "country", "deposit", "expense", "is_active",
        "nit","type_taxpayer_id","code", "gire_id", "country_id", "state_id", "municipality_id"
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function countries1()
    {
        return $this->belongsTo('App\Country', 'country_id', 'id');        
    }
    
    public function estado()
    {
        return $this->belongsTo('App\State', 'state_id', 'id');        
    }
    
    public function municipio()
    {
        return $this->belongsTo('App\Municipality', 'municipality_id','id');        
    }
    
    public function gire()
    {
        return $this->belongsTo('App\Gire', 'gire_id', 'id');        
    }

    public function taxpayer()
    {
        return $this->belongsTo('App\Type_taxpayer');        
    }
}
