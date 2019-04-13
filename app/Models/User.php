<?php namespace App\Models ;

	use Illuminate\Database\Eloquent\Model ;

	/**
	 * 
	 */
	class User extends Model
	{
		
		protected $fillable = [

			'username',
			'name',
			'surname',
			'phone',
			'country',
			'role',
			'is_active',
			'is_blocked',
			'email',
			'email_verified_at',
			'password',
			'remember_token',

		] ;


	    /**
	     * The attributes that should be hidden for arrays.
	     *
	     * @var array
	     */
	    protected $hidden = [
	        'password', 'remember_token',
	    ];


	    /**
	    * User Proof of payment.
	    */
	    public function proof() {

	        return $this->hasOne( ProofOfActivation::class ) ;

	    }
	    /**
	    * User's account.
	    */
	    public function account() {

	        return $this->hasOne( Account::class ) ;

	    }
	    /**
	    * Users's level.
	    */
	    public function level() {

	        return $this->hasOne( Level::class ) ;

	    }


	}