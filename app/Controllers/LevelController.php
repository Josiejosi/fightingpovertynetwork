<?php namespace App\Controllers ;

	use App\Models\Level ;

	class LevelController extends Controller {

		public function add( $request, $response ) {

			$level 					= Level::create([

		        'name'				=> 1, 
		        'description' 		=> 'Level 1', 
		        'amount' 			=> 200, 
		        'upgrade_amount'	=> 400,
		        'auto_upgrade' 		=> 0,
		        'profit'			=> 400,

			]) ;

			$level 					= Level::create([

		        'name'				=> 1, 
		        'description' 		=> 'Level 2', 
		        'amount' 			=> 400, 
		        'upgrade_amount'	=> 800,
		        'auto_upgrade' 		=> 0,
		        'profit'			=> 1600,

			]) ;

			$level 					= Level::create([

		        'name'				=> 3, 
		        'description' 		=> 'Level 3', 
		        'amount' 			=> 800, 
		        'upgrade_amount'	=> 6400,
		        'auto_upgrade' 		=> 0,
		        'profit'			=> 4800,

			]) ;

			$level 					= Level::create([

		        'name'				=> 4, 
		        'description' 		=> 'Level 4', 
		        'amount' 			=> 1600, 
		        'upgrade_amount'	=> 25600,
		        'auto_upgrade' 		=> 0,
		        'profit'			=> 25600,

			]) ;

			$this->flash->addMessage( 'success', 'All levels added.' ) ;
			return $response->withRedirect( $this->router->pathFor( 'home' ) ) ;

		}

	}