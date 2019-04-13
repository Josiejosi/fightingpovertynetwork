<?php

namespace App\Models ;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
	public $timestamps = false ;

    protected $fillable = [

        'name', 
        'description', 
        'amount', 
        'upgrade_amount',
        'auto_upgrade',
        'profit',

    ] ;

    public function user()  {

        return $this->belongsTo( User::class ) ;

    }
}
