<?php namespace App\Classes ;

	use App\Models\User ;
	use App\Models\Downliner ;
	use App\Models\IncomingAmount ;

	class Upgrader extends Helpers {

		public function MoveLevelTwo( $user_id ) {

	    	$downliners 								= Downliner::whereUserId( $user_id )->get() ;

	    	$user 										= User::find( $user_id ) ;

	    	$amount 									= 400 ;
	    	
	    	$this->Level->upgradeLevel( $user_id, 2 ) ;

	    	foreach ( $downliners as $downliner ) {

	    		$downliner_id 							= $downliner->downliner_id ;
	    		//$this->addUpdateOrders( $user_id, $downliner_id, 2 ) ;

	    		$third_downliners 						= Downliner::whereUserId( $downliner_id )->get() ;

	    		foreach ( $third_downliners as $third ) {

		    		$downliner_id 						= $third->downliner_id ;
		    		$this->addUpdateOrders( $user_id, $downliner_id, 2, $amount ) ;

				}

	    	}

		}

		public function MoveLevelThree( $user_id ) {

	    	$downliners 								= Downliner::whereUserId( $user_id )->get() ;

	    	$user 										= User::find( $user_id ) ;

	    	$amount 									= 800 ;
	    	
	    	$this->Level->upgradeLevel( $user_id, 3 ) ;

	    	foreach ( $downliners as $downliner ) {

	    		$downliner_id 							= $downliner->downliner_id ;

	    		$third_downliners 						= Downliner::whereUserId( $downliner_id )->get() ;

	    		foreach ( $third_downliners as $third ) {

		    		$downliner_id 						= $third->downliner_id ;

		    		$fouth_downliners 						= Downliner::whereUserId( $downliner_id )->get() ;

		    		foreach ( $fouth_downliners as $fouth ) {

			    		$downliner_id 						= $fouth->downliner_id ;
			    		$this->addUpdateOrders( $user_id, $downliner_id, 3, $amount ) ;


					}

				}

	    	}

		}

		public function MoveLevelFour( $user_id ) {

	    	$downliners 								= Downliner::whereUserId( $user_id )->get() ;

	    	$user 										= User::find( $user_id ) ;

	    	$amount 									= 1600 ;
	    	
	    	$this->Level->upgradeLevel( $user_id, 4 ) ;

	    	foreach ( $downliners as $downliner ) {

	    		$downliner_id 							= $downliner->downliner_id ;

	    		$third_downliners 						= Downliner::whereUserId( $downliner_id )->get() ;

	    		foreach ( $third_downliners as $third ) {

		    		$downliner_id 						= $third->downliner_id ;

		    		$fouth_downliners 					= Downliner::whereUserId( $downliner_id )->get() ;

		    		foreach ( $fouth_downliners as $fouth ) {

			    		$downliner_id 					= $fouth->downliner_id ;

			    		$firth_downliners 				= Downliner::whereUserId( $downliner_id )->get() ;

			    		foreach ( $firth_downliners as $firth ) {

				    		$downliner_id 				= $firth->downliner_id ;
				    		$this->addUpdateOrders( $user_id, $downliner_id, 4, $amount ) ;


						}

					}

				}

	    	}

		}

		private function addUpdateOrders( $upliner_id, $downliner_id, $level, $amount ) {

    		$down 										= User::find( $downliner_id ) ;
    		$user 										= User::find( $upliner_id ) ;

	        IncomingAmount::create([

		        'amount' 								=> $amount,
		        'status' 								=> 0,
		        'receiver_id'							=> $upliner_id,
		        'sender_id' 							=> $downliner_id,

	        ]) ;

			$body 										= "Hi " . $down->name . " " . $down->name . "<br><br>"
														. $user->name . " " . $user->name . " Has made a level 2 upgrade request DEtails: <br>"
														. "<strong>Username:</strong> . $user->username .<br>"
														. "<strong>Email:</strong>" . $user->email . "<br>"
														. "<strong>Phone:</strong>" . $user->phone . "<br>"
														. "<strong>Bank:</strong>" . $user->account->bank_name . "<br>"
														. "<strong>Account Number:</strong>" . $user->account->account_number . "<br>"
														. "<strong>Account Type:</strong>" . $user->account->account_type . "<br>"
														. "<strong>Amount:</strong>R 400<br>" ;

			$subject 									= "FPN - Level $level Upgrade" ;
			Queue::add( $subject, $body, $downliner_id ) ;

		}

	}