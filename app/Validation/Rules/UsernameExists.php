<?php namespace App\Validation\Rules ;

	use Respect\Validation\Rules\AbstractRule ;

	/**
	 *  UsernameExists
	 */
	class UsernameExists extends AbstractRule
	{
		
		public function validate( $input )
		{

			return \App\Models\User::where( 'username', $input )->count() === 0 ;

		}

	}