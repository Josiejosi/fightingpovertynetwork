<?php namespace App\Controllers ;

	class FrontController extends Controller {

		public function index( $request, $response ) {

			$data 								= [

				'title' 						=> 'Welcome to FPN',

			] ;

			return $this->view->render( $response, 'front/home.twig', $data );

		}


	}