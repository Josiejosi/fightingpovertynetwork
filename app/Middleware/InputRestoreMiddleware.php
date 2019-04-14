<?php namespace App\Middleware ;

	/**
	 * InputRestoreMiddleware
	 */
	
	class InputRestoreMiddleware extends Middleware
	{
		
		public function __invoke( $request, $response, $next ) {

			$this->view->getEnvironment()->addGlobal( 'old', $_SESSION['old'] ) ;
			$_SESSION['old'] = $request->getParams() ;

			return $next( $request, $response ) ;

		}
	}