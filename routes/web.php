<?php 

	use App\Middleware\AuthMiddleware ;
	use App\Middleware\GuestMiddleware ;

	/**
	 * This is where you put all your routes.
	 */
	

	$app->group( '', function() {

		$this->get( '/', 							'FrontController:index' )->setName('home') ;
		
		$this->get( '/register', 					'AuthController:register' )->setName('register') ;
		$this->post( '/register', 					'AuthController:postRegister' ) ;
		
		$this->get( '/register/{username}', 		'AuthController:referral' )->setName('referral') ;
		$this->post( '/referral', 					'AuthController:postReferral' ) ;

		$this->get( '/login', 						'AuthController:login' )->setName('login') ;
		$this->post( '/login', 						'AuthController:postLogin' ) ;

		$this->get( '/password/reset', 				'AuthController:reset' )->setName('reset') ;
		$this->post( '/password/reset', 			'AuthController:postReset' ) ;

		$this->get( '/reset/{email_token}', 		'AuthController:reset_password' ) ;
		$this->post( '/reset/password', 			'AuthController:postPasswordReset' ) ;

		$this->get( '/contact_us', 					'ContactController:index' )->setName('contact') ;
		$this->post( '/contact_us', 				'ContactController:postContact' ) ;

		$this->get( '/about_us', 					'FrontController:about' )->setName('about') ;

		$this->get( '/cron/send/mail', 				'CronController:sendMail' )->setName('send_mail') ;	

		//Temp remove later.
		//
		

	})->add( new GuestMiddleware( $container ) ) ;

	$app->group( '', function() {

		$this->get( '/activation', 					'ActivationController:index' )->setName('activation') ;

		$this->get( '/incoming', 					'IncomingController:index' )->setName('incoming') ;
		$this->get( '/order/pay/{id}', 				'IncomingController:pay' ) ;
		$this->get( '/order/approve/{id}', 			'IncomingController:approve' ) ;

		$this->get( '/outgoing', 					'OutgoingController:index' )->setName('outgoing') ;

		$this->get( '/dashboard', 					'HomeController:index' )->setName('dashboard') ;
		$this->get( '/member/details/{id}', 		'HomeController:member' ) ;

		$this->get( '/upgrade', 					'HomeController:upgrade' )->setName('upgrade') ;

		$this->get( '/upliners', 					'HomeController:upliner' )->setName('upliner') ;
		$this->get( '/downliners', 					'HomeController:downliner' )->setName('downliner') ;

		$this->get( '/account', 					'HomeController:account' )->setName('account') ;
		$this->post( '/account', 					'HomeController:postAccount' ) ;

		$this->get( '/structure', 					'HomeController:structure' )->setName('structure') ;
		$this->get( '/member/structure', 			'HomeController:load_structure' ) ;

		$this->get( '/profile', 					'ProfileController:index' )->setName('profile') ;
		$this->post( '/profile', 					'ProfileController:postProfileUpdate' ) ;

		$this->get( '/password/update', 			'ProfileController:password_update' )->setName('password_update') ;
		$this->post( '/password/update', 			'ProfileController:postPasswordUpdate' ) ;

		//Admin Controller.  
		//
		$this->get( '/admin', 						'AdminController:admin' )->setName('admin') ;
		$this->post( '/admin', 						'AdminController:postAdmin' ) ;
		$this->get( '/users', 						'AdminController:users' )->setName('users') ;
		$this->get( '/orders', 						'AdminController:orders' )->setName('orders') ;
		$this->get( '/edit/details/{id}', 			'AdminController:user' )->setName('user') ;
		$this->post( '/edit/details', 				'AdminController:postUser' ) ;
		$this->get( '/edit/account/{id}', 			'AdminController:account' )->setName('account') ;
		$this->get( '/delete/account/{id}', 		'AdminController:member_delete' )->setName('member_delete') ;
		$this->post( '/edit/account', 				'AdminController:postAccount' ) ;
		$this->get( '/order/delete/{id}', 			'AdminController:orderDelete' ) ;

		$this->get( '/admin/downliners', 			'AdminController:downliners' )->setName('admin_downliners') ;
		$this->get( '/delete/downliner/{id}', 		'AdminController:postDeleteDownliners' ) ;

		$this->get( '/user/password/{id}', 			'AdminController:user_password' )->setName('user_password') ;
		$this->post( '/user/password', 				'AdminController:change_password' ) ;

		$this->get( '/logout', 						'AuthController:logout' )->setName('logout') ;

	})->add( new AuthMiddleware( $container ) ) ;
