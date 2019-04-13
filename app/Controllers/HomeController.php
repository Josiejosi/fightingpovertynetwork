<?php namespace App\Controllers ;

	class HomeController extends Controller {

		public function index( $request, $response ) {

			if ( $this->auth->user()->is_active == 0 ) {

				$this->flash->addMessage( 'error', 'Upliner needs to confirm receiving activation FUNDS from you first' ) ;
				return $response->withRedirect( $this->router->pathFor( 'activation' ) ) ;
				
			}

			if ( $this->auth->user()->is_blocked == 1 ) {

				$this->flash->addMessage( 'error', 'Account is blocked due to none payment to upliner.' ) ;
				return $response->withRedirect( $this->router->pathFor( 'logout' ) ) ;

			}

			$data 								= [

				'title' 						=> 'Home',

			] ;

			return $this->view->render( $response, 'app/home.twig', $data );

		}


	}