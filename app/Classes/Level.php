<?php namespace App\Classes ;

	use App\Models\UserLevel ;
	use App\Models\UserCompletedLevel ;

    use App\Classes\Queue ;

	class Level extends Helpers {

        public function getLevel() {

            $level_id                   = 1 ;

            $user_id 					= $this->auth->id() ;

            if ( $this->auth->check() ) {

                if ( UserLevel::whereUserId( $user_id )->count() > 0 ) {

                    $level              = UserLevel::whereUserId( $user_id )->first() ;
                    $level_id           = $level->level_id ;

                }

            }
            return $level_id ;
        }

        public function upgradeLevel( $user_id, $level ) {

            if ( UserLevel::whereUserId( $user_id )->count() > 0 ) {

                $level                  = UserLevel::whereUserId( $user_id )->first() ;
                $level->delete() ;

            }

            UserLevel::create([

                'level_id'              => (int) $level, 
                'user_id'               => (int) $user_id,

            ]) ;


        }

        public function currentLevel() {

        	$user_id                   = $this->auth->id() ;

            return UserCompletedLevel::whereUserId( $user_id )->whereIsLevelStarted(1)->whereIsLevelComplete(0)->first() ;

        }

        public function incrementLevelPay( $upliner_id ) {

            $current_level              = $this->currentLevel() ;

            if ( $current_level->upgrade_count == 2 ) {

                $current_level->update([ 'upgrade_count' => 3, ]) ;


            } else if ( $current_level->upgrade_count == 1 ) {

                $current_level->update([ 'upgrade_count' => 2, ]) ;


            } else if ( $current_level->upgrade_count == 0 ) {

                $current_level->update([ 'upgrade_count' => 1, ]) ;

            }

        }

	}