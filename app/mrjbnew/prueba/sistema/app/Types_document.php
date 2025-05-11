<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Types_document extends Model
{
    //
      protected $fillable =[
        "id", "documento", "resolucion", "serie", "correlativo", "created_at","updated_at", "modulo"
    ];

}
