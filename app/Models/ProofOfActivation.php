<?php

namespace App\Models ;

use Illuminate\Database\Eloquent\Model;

class ProofOfActivation extends Model {

    protected $fillable = [

        'proof_of_payment', 
        'user_id',

    ] ;

    public function user()  {

        return $this->belongsTo( User::class ) ;

    }

}
