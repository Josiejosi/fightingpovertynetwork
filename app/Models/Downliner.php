<?php

namespace App\Models ;

use Illuminate\Database\Eloquent\Model;

class Downliner extends Model
{
	public $timestamps = false ;

    protected $fillable = [

        'user_id',
        'downliner_id',

    ] ;
}
