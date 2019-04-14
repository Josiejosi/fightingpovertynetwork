<?php namespace App\Controllers ;

	use App\Models\User ;
	use App\Models\IncomingAmount ;


	class IncomingController extends Controller {

		public function index( $request, $response ) {

			$user_id 							= $this->auth->id() ;

			$incoming               			= IncomingAmount::whereReceiverId( $user_id )->get() ;

			$data 								= [

				'title' 						=> 'Home',
				'incoming' 						=> $incoming,

			] ;

			return $this->view->render( $response, 'app/incoming.twig', $data );

		}

	    public function approve( $request, $response, $args ) {

	    	$incoming               			= IncomingAmount::find( $args[ "id" ] ) ;

	        $sender_id              			= $incoming->sender_id ;
	    	$receiver_id            			= $incoming->receiver_id ;



	    	if ( $incoming->status == 1 || $incoming->status == 0 ) {
	    		
	    		$incoming->update([ 'status' => 2 ]) ;

	    		$user              				= User::find( $sender_id ) ;

	    		if ( $user->is_active == 0 ) {

	    			//send activation email

	    			$user->update( [ 'is_active' => true ] ) ;

	    		}

	            //$this->Level->incrementLevelPay( $this->auth->id() ) ;

				$this->flash->addMessage( 'success', 'Approved receiving payment from downliner.' ) ;
				return $response->withRedirect( $this->router->pathFor( 'dashboard' ) ) ;

	    	}

	    }


	    public function pay( $request, $response, $args ) {

	    	$incoming 							= IncomingAmount::find( $args[ "id" ] ) ;

	    	$sender_id 							= $incoming->sender_id ;

	    	if ( $incoming->status == 0 ) {
	    		
	    		$incoming->update([ 'status' => 1 ]) ;

				$this->flash->addMessage( 'success', 'You just made payment to upliner.' ) ;
				return $response->withRedirect( $this->router->pathFor( 'dashboard' ) ) ;

	    	}

	    }


	}