<?php namespace App\Models ;

	use Illuminate\Database\Eloquent\Model ;

	/**
	 * 
	 */
	class QueueJob extends Model
	{

		protected $table = 'queue_jobs' ;
		
		protected $fillable = [

			'email',
			'subject',
			'body',
			'is_sent',
			'user_id',

		] ;

	}