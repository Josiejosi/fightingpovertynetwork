<?php namespace App\Validation\Exceptions ;


	use Respect\Validation\Exceptions\ValidationException ;

	/**
	 *  UsernameExistsException
	 */
	class UsernameExistsException extends ValidationException
	{
		
		public static $defaultTemplates = [

			self::MODE_DEFAULT 			=> [

				self::STANDARD 			=> 'Username already taken',

			] 

		] ;

	}