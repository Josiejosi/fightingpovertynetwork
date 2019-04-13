<?php

namespace App\Models ;

use Illuminate\Database\Eloquent\Model ;

class IncomingAmount extends Model {

    protected $fillable = [

        'amount',
        'status',
        'receiver_id',
        'sender_id',

    ] ;
    
}
