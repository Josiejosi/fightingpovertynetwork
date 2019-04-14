<?php namespace App\Controllers ;

	use App\Models\User ;
	use App\Models\IncomingAmount ;


	class OutgoingController extends Controller {

		public function index( $request, $response ) {

			$user_id 							= $this->auth->id() ;

			$outgoing               			= IncomingAmount::whereSenderId( $user_id )->get() ;

			$data 								= [

				'title' 						=> 'Home',
				'outgoing' 						=> $outgoing,

			] ;

			return $this->view->render( $response, 'app/outgoing.twig', $data );

		}


	}