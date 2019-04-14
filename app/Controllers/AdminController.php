<?php namespace App\Controllers ;

	use App\Models\User ;
	use App\Models\IncomingAmount ;

	class AdminController extends Controller {

		public function users( $request, $response ) {

			$users 								= User::whereRole(1)->get() ;

			$data 								= [

				'title' 						=> 'Home',
				'users' 						=> $users,

			] ;

			return $this->view->render( $response, 'admin/users.twig', $data );

		}

		public function orders( $request, $response ) {

			$orders 							= IncomingAmount::orderBy('created_at', 'desc')->get() ;

			$data 								= [

				'title' 						=> 'Home',
				'orders' 						=> $orders,

			] ;

			return $this->view->render( $response, 'admin/orders.twig', $data );

		}


	}