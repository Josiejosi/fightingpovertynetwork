<?php namespace App\Controllers ;

	use Respect\Validation\Validator as v ;

	use App\Models\User ;
	use App\Models\Upliner ;
	use App\Models\Account ;
	use App\Models\UserLevel ;
	use App\Models\Downliner ;
	use App\Models\IncomingAmount ;
	use App\Models\UserCompletedLevel ;

	use App\Classes\Auth ;
	use App\Classes\Queue ;

	use Carbon\Carbon ;

	class AdminController extends Controller {

		public function admin( $request, $response ) {

			$data 								= [

				'title' 						=> 'New Admin',

			] ;

			return $this->view->render( $response, 'admin/admin.twig', $data );

		}

		public function postAdmin( $request, $response ) {

			$validation 						= $this->validator->validate( $request, [

				'username'						=> v::notEmpty()->usernameExists(),
				'email'							=> v::notEmpty()->email(),
				'name'							=> v::notEmpty()->alpha(),
				'surname'						=> v::notEmpty()->alpha(),
				'phone_number'					=> v::notEmpty()->numeric()->phoneExists(),
				'country'						=> v::notEmpty(),
				'bank'							=> v::notEmpty(),
				'account_number'				=> v::notEmpty(),
				'account_type'					=> v::notEmpty(),
				'upliner_name'					=> v::notEmpty(),
				'password'						=> v::notEmpty(),

			]) ;

			$referral_code 						= rand( 111111, 999999 ) ;
			$email_token 						= md5( $referral_code ) ;
			$password 							= $request->getParam( 'password' ) ;
			$hashed_password 					= password_hash( $password, PASSWORD_DEFAULT ) ;
			

			$user 								= User::create([
				'username'						=> $request->getParam( 'username' ),
				'name' 							=> $request->getParam( 'name' ),
				'surname' 						=> $request->getParam( 'surname' ),
				'phone' 						=> $request->getParam( 'phone_number' ),
				'country' 						=> $request->getParam( 'country' ),
				'role' 							=> 2,
				'is_active' 					=> 1,
				'is_blocked' 					=> 0,
				'email' 						=> $request->getParam( 'email' ),
				'email_verified_at'				=> Carbon::now(),
				'password' 						=> $hashed_password,
				'remember_token' 				=> $email_token,
			]) ;

			$account 							= Account::create([ //Account

		        'bank_name'						=> $request->getParam( 'bank' ), 
		        'account_holder' 				=> $request->getParam( 'name' ) . " " . $request->getParam( 'surname' ), 
		        'account_number' 				=> $request->getParam( 'account_number' ), 
		        'account_type' 					=> $request->getParam( 'account_type' ),

		        'user_id' 						=>  $user->id,

			]) ;

	        UserLevel::create([

	            'level_id'              		=> 1, 
	            'user_id'               		=> $user->id,


	        ]) ;

	        UserCompletedLevel::create([

	            'level'                 		=> 1,
	            'is_level_started'      		=> 1,
	            'is_level_complete'     		=> 0,
	            'upgrade_count'         		=> 0,
	            'user_id'               		=> $user->id,

	        ]) ;


	        UserCompletedLevel::create([

	            'level'                 		=> 2,
	            'is_level_started'      		=> 0,
	            'is_level_complete'     		=> 0,
	            'upgrade_count'         		=> 0,
	            'user_id'               		=> $user->id,

	        ]) ;


	        UserCompletedLevel::create([

	            'level'                 		=> 3,
	            'is_level_started'      		=> 0,
	            'is_level_complete'     		=> 0,
	            'upgrade_count'         		=> 0,
	            'user_id'               		=> $user->id,

	        ]) ;


	        UserCompletedLevel::create([

	            'level'                 		=> 4,
	            'is_level_started'      		=> 0,
	            'is_level_complete'     		=> 0,
	            'upgrade_count'         		=> 0,
	            'user_id'               		=> $user->id,

	        ]) ;


			$body 								= "Welcome " . $request->getParam( 'name' ) . "<br><br>"
												. "Your admin account was created successfully, Login Details: <br>"
												. "<strong>Username:</strong> " . $request->getParam( 'username' ) . "<br>"
												. "<strong>Password: </strong>$password" ;

			$subject 							= "Welcome to FPN" ;
			Queue::add( $subject, $body, $user->id ) ;

			$auth = $this->auth->attempt( $request->getParam( 'username' ), $request->getParam( 'password' ) ) ;

			if ( $auth ) {

				$this->flash->addMessage( 'success', 'Successfully logged in.' ) ;

				return $response->withRedirect( $this->router->pathFor( 'admin' ) ) ;

			}

		}

		public function users( $request, $response ) {

			$users 								= User::whereRole(1)->get() ;

			$data 								= [

				'title' 						=> 'All Users',
				'users' 						=> $users,

			] ;

			return $this->view->render( $response, 'admin/users.twig', $data );

		}

		public function orders( $request, $response ) {

			$orders 							= IncomingAmount::orderBy('created_at', 'desc')->get() ;

			$data 								= [

				'title' 						=> 'All Orders',
				'orders' 						=> $orders,

			] ;

			return $this->view->render( $response, 'admin/orders.twig', $data );

		}

		public function user( $request, $response, $args  ) {

			$user 								= User::find( $args[ "id" ] ) ;

			$data 								= [

				'title' 						=> 'Member Details',
				'user'              			=> $user,
	            
			] ;

			return $this->view->render( $response, 'admin/details.twig', $data ) ;
		}

		public function postUser( $request, $response ) {

			$validation 						= $this->validator->validate( $request, [

				'name'							=> v::notEmpty()->alpha(),
				'surname'						=> v::notEmpty()->alpha(),
				'phone_number'					=> v::notEmpty()->numeric(),

			]) ;

			if ( $validation->failed() ) {

				$this->flash->addMessage( 'warning', 'Please check your form fields for any errors' ) ;

				return $response->withRedirect( $this->router->pathFor( 'profile' ) ) ;

			}

			$user 								= ( User::find( $request->getParam( 'id' ) ) )->update([
				'email'							=> $request->getParam( 'email' ),
				'name'							=> $request->getParam( 'name' ),
				'surname'						=> $request->getParam( 'surname' ),
				'phone'							=> $request->getParam( 'phone_number' ),
			]) ;

			if ( $user )
				$this->flash->addMessage( 'success', 'Profile was updated successfully.' ) ;
			else
				$this->flash->addMessage( 'warning', 'Failed to update your profile.' ) ;

			return $response->withRedirect( $this->router->pathFor( 'users' ) ) ;
		}

		public function account( $request, $response, $args  ) {

			$user 								= User::find( $args[ "id" ] ) ;

			$data 								= [

				'title' 						=> 'Member Details',
				'user'              			=> $user,
	            
			] ;

			return $this->view->render( $response, 'admin/account.twig', $data ) ;
		}

		public function postAccount( $request, $response ) {

			$account 							= Account::whereUserId( $request->getParam( 'id' ) )->first() ;

			$account->update([

		        'bank_name'						=> $request->getParam( 'bank_name' ), 
		        'account_number' 				=> $request->getParam( 'account_number' ), 
		        'account_type' 					=> $request->getParam( 'account_type' ),

			]) ;

			$this->flash->addMessage( 'success', 'Banking Details successfully updated.' ) ;
			return $response->withRedirect( $this->router->pathFor( 'users' ) ) ;

		}

		public function orderDelete( $request, $response, $args ) {

			$order 								= IncomingAmount::find( $args[ "id" ] ) ;

			$order->delete() ;

			$this->flash->addMessage( 'success', 'Banking Details successfully updated.' ) ;
			return $response->withRedirect( $this->router->pathFor( 'users' ) ) ;

		}

		public function member_delete( $request, $response, $args  ) {

			$user 								= User::find( $args[ "id" ] ) ;

			$user->delete() ;

			$this->flash->addMessage( 'info', 'Member removed.' ) ;
			return $response->withRedirect( $this->router->pathFor( 'dashboard' ) ) ;
		}


	}