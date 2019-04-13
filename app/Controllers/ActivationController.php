<?php namespace App\Controllers ;

	use App\Models\User ;
	use App\Models\Upliner ;

	class ActivationController extends Controller {

		public function index( $request, $response ) {

			$user_id 							= $this->auth->id() ;

			$upliner_link 						= Upliner::whereUserId( $user_id )->first() ;
			$upliner 							= User::find( $upliner_link->upliner_id ) ;

			$data 								= [

				'title' 						=> 'Home',
				'upliner' 						=> $upliner,

			] ;

			return $this->view->render( $response, 'auth/activation.twig', $data );

		}


	}