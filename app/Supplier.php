<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable =[

        "name", "image", "company_name", "vat_number",
        "email", "phone_number", "address", "city",
        "state", "postal_code", "country", "is_active",
        "gire_id", "country_id", "state_id", "municipality_id"
        
    ];

    public function product()
    {
    	return $this->hasMany('App/Product');    	
    }

    public function countries()
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
}
