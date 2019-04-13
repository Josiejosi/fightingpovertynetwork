<?php

namespace App\Models ;

use Illuminate\Database\Eloquent\Model ;

class Account extends Model {

	public $timestamps = false ;

    protected $fillable = [

        'bank_name', 
        'account_holder', 
        'account_number', 
        'account_type',

        'user_id',

    ] ;

    public function user()  {

        return $this->belongsTo( User::class ) ;

    }

}
