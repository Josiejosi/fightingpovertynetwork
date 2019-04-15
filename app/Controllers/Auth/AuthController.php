<?php namespace App\Controllers\Auth ;


	use Respect\Validation\Validator as v ;

	use App\Controllers\Controller ;

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


	class AuthController extends Controller {

		public function register( $request, $response ) {

			$data 								= [

				'title' 						=> 'Registration',

			] ;

			return $this->view->render( $response, 'auth/register.twig', $data );

		}

		public function referral( $request, $response, $args ) {

			$username 							= $args[ "username" ] ;

			$user 								= [] ;

			if ( User::whereUsername( $username )->whereIsActive(1)->whereIsBlocked(0)->count() > 0 ) {

				$user 							= User::whereUsername( $username )->first() ;

			}

			$data 								= [

				'title' 						=> 'Registration',
				'user' 							=> $user,

			] ;

			return $this->view->render( $response, 'auth/referral.twig', $data );

		}

		public function postRegister( $request, $response ) {

			$validation 						= $this->validator->validate( $request, [

				'username'						=> v::notEmpty()->usernameExists(),
				'email'							=> v::notEmpty()->email(),
				'name'							=> v::notEmpty()->alpha(),
				'surname'						=> v::notEmpty()->alpha(),
				'phone_number'					=> v::notEmpty()->numeric(),
				'country'						=> v::notEmpty(),
				'bank'							=> v::notEmpty(),
				'account_number'				=> v::notEmpty(),
				'account_type'					=> v::notEmpty(),
				'upliner_name'					=> v::notEmpty(),
				'password'						=> v::notEmpty(),

			]) ;

			if ( $validation->failed() ) {

				$this->flash->addMessage( 'warning', 'Please check your form fields for any errors' ) ;
				return $response->withRedirect( $this->router->pathFor( 'register' ) ) ;

			}

			if ( User::whereUsername( $request->getParam( 'upliner_name' ) )->count() == 0 ) {

				$this->flash->addMessage( 'warning', 'Upliner username is not found.' ) ;
				return $response->withRedirect( $this->router->pathFor( 'register' ) ) ;

			}

			$upliner 							= User::whereUsername( $request->getParam( 'upliner_name' ) )->first() ;

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
				'role' 							=> 1,
				'is_active' 					=> 0,
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


	        if ( Downliner::whereUserId( $upliner->id )->count() < 2 ) {

		        $this->addDownliner( $user->id, $upliner->id ) ;

		    } else {

		    	$downliners 					= Downliner::whereUserId( $upliner->id )->get() ;

		    	$level_one_found 				= false ;

		    	foreach ( $downliners as $downliner ) {

		    		$downliner_id 				= $downliner->downliner_id ;

		    		if ( Downliner::whereUserId( $downliner_id )->count() < 2 ) {

		    			$this->addDownliner( $user->id, $downliner_id ) ;

		    			$level_one_found 		= true ;

		    			break ;

		    		}

		    	}

		    	if ( $level_one_found == false ) {

			    	foreach ( $downliners as $downliner ) {

			    		$downliner_id 				= $downliner->downliner_id ;

			    		$third_downliners 			= Downliner::whereUserId( $downliner_id )->get() ;

			    		foreach ( $third_downliners as $third ) {

			    			$downliner_id 			= $third->downliner_id ;

				    		if ( Downliner::whereUserId( $downliner_id )->count() < 2 ) {

				    			$this->addDownliner( $user->id, $downliner_id ) ;
				    			break ;

				    		}

			    		}


			    	}

		    	}

		    }


			$body 								= "Welcome " . $request->getParam( 'name' ) . "<br><br>"
												. "Your account was created successfully, Login Details: <br>"
												. "<strong>Username:</strong> " . $request->getParam( 'username' ) . "<br>"
												. "<strong>Password: </strong>$password" ;

			$subject 							= "Welcome to FPN" ;
			Queue::add( $subject, $body, $user->id ) ;

			$auth = $this->auth->attempt( $request->getParam( 'username' ), $request->getParam( 'password' ) ) ;

			if ( $auth ) {

				$this->flash->addMessage( 'success', 'Successfully logged in.' ) ;

				return $response->withRedirect( $this->router->pathFor( 'activation' ) ) ;

			}

		}

		public function login( $request, $response ) {

			$data 								= [

				'title' 						=> 'Login',

			] ;

			return $this->view->render( $response, 'auth/login.twig', $data );

		}

		public function postLogin( $request, $response ) {

			$auth = $this->auth->attempt( $request->getParam( 'email' ), $request->getParam( 'password' ) ) ;

			if ( $auth ) {

				$this->flash->addMessage( 'success', 'Successfully logged in.' ) ;

				return $response->withRedirect( $this->router->pathFor( 'dashboard' ) ) ;

			}

			$this->flash->addMessage( 'warning', 'Wrong combination to authenticate to the account.' ) ;

			return $response->withRedirect( $this->router->pathFor( 'login' ) ) ;

		}

		public function reset( $request, $response ) {

			$data 								= [

				'title' 						=> 'Forgot Password',

			] ;

			return $this->view->render( $response, 'auth/reset.twig', $data );

		}

		public function postReset( $request, $response ) {

			$email 								= $request->getParam( 'email' ) ;

			if ( User::where('email', $email)->count() == 1 ) {

				$user 							= User::where( 'email', $email )->first() ;

				$email_token 					= $user->email_token ;

				$app_url 						= getenv( "APP_URL" ) . '/reset/' . $email_token ;

				$this->HelpAuth->sendForgotPasswwordEmail( $user, $app_url ) ;

				$this->flash->addMessage( 'success', 'Please check your emails for a reset link.' ) ;

			} else {

				$this->flash->addMessage( 'warning', 'Specified email not found in our records.' ) ;

			}

			return $response->withRedirect( $this->router->pathFor( 'reset' ) ) ;

		}

		public function reset_password( $request, $response, $args ) {

			$data 								= [

				'title' 						=> 'Reset Password',
				'email_token' 					=> $args[ "email_token" ],

			] ;

			return $this->view->render( $response, 'auth/reset_password.twig', $data );

		}

		public function postPasswordReset( $request, $response ) {

			$email_token 						= $request->getParam( 'email_token' ) ;
			$password 							= $request->getParam( 'password' ) ;
			$confirm_password 					= $request->getParam( 'confirm_password' ) ;


			if ( User::where('email_token', $email_token)->count() == 0 ) {

				$this->flash->addMessage( 'warning', 'Reset Token has expired, Please try and reset password again.' ) ;

				return $response->withRedirect( '/reset/' . $email_token ) ;

			}

			if ( $password != $confirm_password ) {

				$this->flash->addMessage( 'warning', 'Please confirm password.' ) ;

				return $response->withRedirect( '/reset/' . $email_token ) ;

			}

			$update_user 						= User::where('email_token', $email_token)->update([

				'password'						=> password_hash( $password, PASSWORD_DEFAULT )

			]) ;

			$this->flash->addMessage( 'success', 'Password was successfully reset.' ) ;

			return $response->withRedirect( $this->router->pathFor( 'login' ) ) ;

		}

		public function fakeAdmin( $request, $response ) {

			$referral_code 						= rand( 111111, 999999 ) ;
			$email_token 						= md5( $referral_code ) ;
			$password 							= 'admin123' ;
			$hashed_password 					= password_hash( $password, PASSWORD_DEFAULT ) ;
			

			$user 								= User::create([
				'username'						=> 'admin',
				'name' 							=> 'Admin',
				'surname' 						=> 'Admin',
				'phone' 						=> '0123456789',
				'country' 						=> 'South Africa',
				'role' 							=> 2,
				'is_active' 					=> 1,
				'is_blocked' 					=> 0,
				'email' 						=> 'admin@fightingpovertynetwork.co.za',
				'email_verified_at'				=> Carbon::now(),
				'password' 						=> $hashed_password,
				'remember_token' 				=> $email_token,
			]) ;

			$account 							= Account::create([

		        'bank_name'						=> 'Capitec', 
		        'account_holder' 				=> 'Admin Admin', 
		        'account_number' 				=> '123456789', 
		        'account_type' 					=> 'Savings Account',

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


			$body 								= "Welcome Admin Admin<br><br>"
												. "Your account was created successfully, Login Details: <br>"
												. "<strong>Username:</strong> admin<br>"
												. "<strong>Password: </strong>admin123" ;

			$subject 							= "Welcome to FPN" ;
			Queue::add( $subject, $body, $user->id ) ;

			$this->flash->addMessage( 'success', 'Account created successfully.' ) ;

			$auth = $this->auth->attempt( "admin", "admin123" ) ;

			if ( $auth ) {

				$this->flash->addMessage( 'success', 'Successfully logged in.' ) ;
				return $response->withRedirect( $this->router->pathFor( 'home' ) ) ;

			}

		}

		private function addDownliner( $user_id, $upliner_id ) {

		        Downliner::create([

			        'user_id'					=> $upliner_id,
			        'downliner_id' 				=> $user_id,

		        ]) ;

	            Upliner::create([

	                'user_id'               	=> $user_id,
	                'upliner_id'            	=> $upliner_id,

	            ]) ;

		        IncomingAmount::create([

			        'amount' 					=> 200,
			        'status' 					=> 0,
			        'receiver_id'				=> $upliner_id,
			        'sender_id' 				=> $user_id,

		        ]) ;

		}

		public function logout( $request, $response ) {

			$this->auth->logout() ;

			return $response->withRedirect( $this->router->pathFor( 'home' ) ) ;

		}

	}