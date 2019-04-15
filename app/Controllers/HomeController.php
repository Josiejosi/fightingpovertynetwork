<?php namespace App\Controllers ;

	use App\Models\UserCompletedLevel ;
	use App\Models\IncomingAmount ;

	use App\Models\Downliner ;
	use App\Models\Upliner ;

	use App\Models\UserLevel ;
	use App\Models\Account ;
	use App\Models\User ;

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

	    	$user_id 							= $this->auth->id() ;

	    	$currentLevel 						= $this->Level->currentLevel() ;

	    	$incoming 							= IncomingAmount::whereReceiverId( $user_id )->get() ;
	    	$outgoing 							= IncomingAmount::whereSenderId( $user_id )->get() ;

	    	$incoming_sum 						= IncomingAmount::whereReceiverId( $user_id )->whereStatus( 2 )->get()->sum('amount') ;
	    	$outgoing_sum 						= IncomingAmount::whereSenderId( $user_id )->whereStatus( 2 )->get()->sum('amount') ;

			$data 								= [

				'title' 						=> 'Home',

				'currentLevel'              	=> $currentLevel,

	        	'outgoing' 						=> $outgoing, 
	        	'incoming' 						=> $incoming ,
	        	'incoming_sum' 					=> $incoming_sum,
	        	'outgoing_sum' 					=> $outgoing_sum,
	            


			] ;

			return $this->view->render( $response, 'app/home.twig', $data ) ;

		}

		public function upgrade( $request, $response ) {

			$user_id 							= $this->auth->id() ;

			$upgrade_amount 					= IncomingAmount::whereReceiverId( $user_id )->whereStatus( 2 )->get()->sum('amount') ;

			if ( $upgrade_amount >= 400 && $upgrade_amount < 800 ) {

				$this->Upgrader->MoveLevelTwo( $user_id ) ;
				$this->flash->addMessage( 'success', 'Successfully upgraded to level 2.' ) ;

			} else if ( $upgrade_amount >= 1600 && $upgrade_amount < 4800 ) {

				$this->Upgrader->MoveLevelThree( $user_id ) ;
				$this->flash->addMessage( 'success', 'Successfully upgraded to level 3.' ) ;

			} else if ( $upgrade_amount >= 4800 ) {

				$this->Upgrader->MoveLevelFour( $user_id ) ;
				$this->flash->addMessage( 'success', 'Successfully upgraded to level 4.' ) ;

			} else {
				$this->flash->addMessage( 'info', 'You dont meet the minimum requirements to upgrade.' ) ;
			}

			
			return $response->withRedirect( $this->router->pathFor( 'dashboard' ) ) ;
		}


		public function member( $request, $response, $args  ) {

			$user 								= User::find( $args[ "id" ] ) ;

			$data 								= [

				'title' 						=> 'Member Details',
				'user'              			=> $user,
	            
			] ;

			return $this->view->render( $response, 'app/member.twig', $data ) ;
		}


		public function upliner( $request, $response, $args  ) {

			$user_id 							= $this->auth->id() ;

			$upliners 							= Upliner::whereUserId( $user_id )->get() ;

			$data 								= [

				'title' 						=> 'My Upliner',
				'upliners'              		=> $upliners,
	            
			] ;

			return $this->view->render( $response, 'app/upliner.twig', $data ) ;
		}

		public function downliner( $request, $response ) {

			$user_id 							= $this->auth->id() ;

			$downliners 						= Downliner::whereUserId( $user_id )->get() ;

			$data 								= [

				'title' 						=> 'My Downliner',
				'downliners'              		=> $downliners,
	            
			] ;

			return $this->view->render( $response, 'app/downliner.twig', $data ) ;
		}

		public function account( $request, $response ) {

			$data 								= [

				'title' 						=> 'Banking Details',
	            
			] ;

			return $this->view->render( $response, 'app/account.twig', $data ) ;
		}

		public function postAccount( $request, $response ) {

			$user_id 							= $this->auth->id() ;

			$account 							= Account::whereUserId( $user_id )->first() ;

			$account->update([

		        'bank_name'						=> $request->getParam( 'bank_name' ), 
		        'account_number' 				=> $request->getParam( 'account_number' ), 
		        'account_type' 					=> $request->getParam( 'account_type' ),

			]) ;

			$this->flash->addMessage( 'success', 'Banking Details successfully updated.' ) ;
			return $response->withRedirect( $this->router->pathFor( 'account' ) ) ;

		}

		public function structure( $request, $response ) {

			$data 								= [

				'title' 						=> 'My Investment Structure',
	            
			] ;

			return $this->view->render( $response, 'app/structure.twig', $data ) ;
		}

		public function load_structure( $request, $response ) {

	    	$owner_level 					= $this->Level->getLevel() ;

			$firstlevel 					= [] ;
			$secondlevel 					= [] ;

			$owner 							= [

				'name' 						=> $this->auth->user()->name . " " . $this->auth->user()->surname,
				'phone' 					=> $this->auth->user()->phone,
				'level' 					=> $owner_level,

			] ;

			$structures 					= Downliner::whereUserId( $this->auth->id() )->orderBy( 'id', 'desc' )->get() ;

			foreach ( $structures as $structure ) {

	            $user 						= User::find( $structure->downliner_id ) ;
	            $level 						= UserLevel::whereUserId($structure->downliner_id)->first() ;

				$firstlevel[]				= [

					'id' 					=> $user->id,
					'name' 					=> $user->name . " " . $user->surname,
					'phone' 				=> $user->phone,
					'level' 				=> $level->level_id,

				] ;

			}

			foreach ( $firstlevel as $member ) {

				$structures 					= Downliner::whereUserId( $member['id'] )->orderBy( 'id', 'desc' )->get() ;

				$secondlevelowner 				= [
					'name' 						=> $member['name'],
					'phone' 					=> $member['phone'],
					'level' 					=> $member['level'],				
				] ;

				$secondlevel 					= [] ;

				foreach ( $structures as $structure ) {

		            $user 						= User::find( $structure->downliner_id ) ;
		            $level 						= UserLevel::whereUserId($structure->downliner_id)->first() ;

					$secondlevel[]				= [

						'name' 					=> $user->name . " " . $user->surname,
						'phone' 				=> $user->phone,
						'level' 				=> $level->level_id,

					] ;

				}

				$secondlevelbuild[] 		    = [$secondlevelowner, 'children'=>$secondlevel] ;
			}

			$results 							= [ 			
				'name' 							=> $this->auth->user()->name . " " . $this->auth->user()->surname,
				'phone' 						=> $this->auth->user()->phone,  
				'children'	 					=> $firstlevel 
			] ;

			header("Content-Type: application/json") ;
			echo json_encode( $results ) ;
			exit;

		}

	}