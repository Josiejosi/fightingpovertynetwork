<?php 

	session_start() ;

	use Respect\Validation\Validator as v ;
	
	require __DIR__ . "/../vendor/autoload.php" ;

	$dotenv 						= new Dotenv\Dotenv( __DIR__ . '/../' ) ;
	$dotenv->load() ;

	//Call all the configuration files.
	//

	$configuration_directory 		= __DIR__ . '/../config/' ;

	$configuration_files 			= [ 'app.php', 'database.php', 'mail.php', ] ;

	$configuration 					= [] ;

	foreach ( $configuration_files as $file ) {
		
		if ( file_exists( $configuration_directory . $file ) ) {

			$configuration 			= array_merge_recursive( 

				$configuration, 
				require $configuration_directory . $file  
			) ;

		}

	}

	$app 							= new \Slim\App([

		'settings' 					=> $configuration ,

	]) ;

	// Fetch DI Container
	//
	$container 						= $app->getContainer() ;

	//Set Global TimeZone
	//
	date_default_timezone_set ( $container['settings']['timezone'] ) ;

	//Create an Eloquent instance.
	//

	try {

		$capsule 						= new \Illuminate\Database\Capsule\Manager ;
		$capsule->addConnection( $container['settings']['db'] ) ;
		$capsule->setAsGlobal() ;
		$capsule->bootEloquent() ;

	} catch( Illuminate\Database\QueryException $exception ) {

	}

	//Register capsule on the container.
	//

	$container['db'] 				= function( $container ) use ( $capsule ) { return $capsule ; } ;

/*	$container['errorHandler'] = function ($container) {
	    return function ($request, $response, $exception) use ($container) {
	        //file_put_contents( __DIR__ . '/../storage/log/' , print_r($exception, true) . ' ');
	        return $response;
	    };
	};

	$container['phpErrorHandler'] = function ($container) {
	    return $container['errorHandler'];
	};*/

	//Auth class binding.
	//
	$container['auth'] 				= function( $container ) { return new \App\Auth\Auth ; } ;
	
	$container['HelpAuth'] 			= function( $container ) { return new \App\Classes\Auth( $container ) ; } ;
	$container['Contact'] 			= function( $container ) { return new \App\Classes\Contact( $container ) ; } ;
	$container['Level'] 			= function( $container ) { return new \App\Classes\Level( $container ) ; } ;
	$container['Upgrader'] 			= function( $container ) { return new \App\Classes\Upgrader( $container ) ; } ;

	//Register our validation class as a global.
	#

	$container['validator'] 		= function( $container ) { return new App\Validation\Validator ; } ;

	$container['flash'] 			= function () { return new \Slim\Flash\Messages() ; } ;

	// Register Twig View helper
	//
	$container['view'] 				= function ( $container ) {

	    $view 						= new \Slim\Views\Twig( __DIR__ . '/../resources/views', [
	        'cache' 				=> false,
	    ]);
	    // Instantiate and add Slim specific extension
	    $router 					= $container->get('router') ;

	    $request   					= $container['request'] ;
		$url       					= $request->getUri() ;
		$path       				= $url->getPath() ;

	    $uri 						= \Slim\Http\Uri::createFromEnvironment( 
	    	new \Slim\Http\Environment( $_SERVER ) 
	    ) ;

	    $view->addExtension( 

	    	new \Slim\Views\TwigExtension( 
		    	$router, 
		    	$uri 
	    	) 

		) ;

		$view->getEnvironment()->addGlobal( 'auth', [

			'check' 				=> $container->auth->check(),
			'user' 					=> $container->auth->user(),
			'id' 					=> $container->auth->id(),

		] ) ;

		$view->getEnvironment()->addGlobal( 'flash', $container->flash ) ;
		$view->getEnvironment()->addGlobal( 'app_name', $container['settings']['app_name'] ) ;
		$view->getEnvironment()->addGlobal( 'app_url', $container['settings']['app_url'] ) ;
		$view->getEnvironment()->addGlobal( 'contact_details', $container['settings']['contact_details'] ) ;
		$view->getEnvironment()->addGlobal( 'Carbon', new Carbon\Carbon ) ;
		$view->getEnvironment()->addGlobal( 'User', new \App\Models\User ) ;
		$view->getEnvironment()->addGlobal( 'Downliner', new \App\Models\Downliner ) ;
		$view->getEnvironment()->addGlobal( 'Level', new \App\Models\UserLevel ) ;

		$view->getEnvironment()->addGlobal( 'route_name', $path) ;

	    return $view;
	    
	} ;

	//Binding routes to controllers
	//
	$container['CronController'] 		= function( $container ) { return new \App\Controllers\CronController( $container ) ; } ;
	$container['AdminController'] 		= function( $container ) { return new \App\Controllers\AdminController( $container ) ; } ;
	$container['UpgradeController'] 	= function( $container ) { return new \App\Controllers\UpgradeController( $container ) ; } ;
	$container['OutgoingController'] 	= function( $container ) { return new \App\Controllers\OutgoingController( $container ) ; } ;
	$container['IncomingController'] 	= function( $container ) { return new \App\Controllers\IncomingController( $container ) ; } ;
	$container['ActivationController'] 	= function( $container ) { return new \App\Controllers\ActivationController( $container ) ; } ;
	$container['FrontController'] 		= function( $container ) { return new \App\Controllers\FrontController( $container ) ; } ;
	$container['LevelController'] 		= function( $container ) { return new \App\Controllers\LevelController( $container ) ; } ;
	
	$container['HomeController'] 		= function( $container ) { return new \App\Controllers\HomeController( $container ) ; } ;
	$container['ContactController'] 	= function( $container ) { return new \App\Controllers\ContactController ( $container ) ; } ;
	$container['ProfileController'] 	= function( $container ) { return new \App\Controllers\ProfileController( $container ) ; } ;
	$container['AuthController'] 		= function( $container ) { return new \App\Controllers\Auth\AuthController( $container ) ; } ;

	//CSRF binding.
	//
	$container['csrf'] 					= function( $container ) { 

	    $guard 							= new \Slim\Csrf\Guard();
	    
	    $guard->setFailureCallable(function ($request, $response, $next) {

	        $request 					= $request->withAttribute( "csrf_status", false ) ;
	        return $next( $request, $response ) ;

	    });

	    return $guard ;

	} ;


	$container['mailer'] 				= function ($container) {

		$mailer 						= new \PHPMailer\PHPMailer\PHPMailer() ;

		$mailer->IsSMTP();

		$mailer->SMTPOptions 			= [
		    'ssl' 						=> [
		        'verify_peer' 			=> false,
		        'verify_peer_name' 		=> false,
		        'allow_self_signed' 	=> true
		    ]
		] ;

		//$mailer->SMTPDebug 				= $container['settings']['debug']; //needed for testing.
		$mailer->SetFrom( $container['settings']['username'] ); 

		$mailer->Host 					= $container['settings']['host'] ;
		$mailer->SMTPAuth 				= $container['settings']['auth'] ;                 // I set false for localhost
		$mailer->SMTPSecure 			= $container['settings']['secure'] ;              // set blank for localhost
		$mailer->Port 					= $container['settings']['port'] ;                           // 25 for local host
		$mailer->Username 				= $container['settings']['username'] ;    // I set sender email in my mailer call
		$mailer->Password 				= $container['settings']['password'] ;
		$mailer->isHTML( true ) ;

		return new \App\Mail\Mailer( $container->view, $mailer );
	};

	//Binding routes to middleware.
	//
	$app->add( new \App\Middleware\ValidationMiddleware( $container ) ) ;
	$app->add( new \App\Middleware\InputRestoreMiddleware( $container ) ) ;
	$app->add( new \App\Middleware\CsrfMiddleware( $container ) ) ;

	$app->add( $container->csrf ) ;

	v::with( 'App\\Validation\\Rules\\' ) ;

	require __DIR__ . "/../routes/web.php" ;
