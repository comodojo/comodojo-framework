<?php namespace Comodojo\Services;

use \Comodojo\Authentication\Broker;
use \Comodojo\Authentication\Token;
use \Comodojo\Users\UserProfile;
use \Comodojo\Dispatcher\Service\Service as DispatcherService;
use \Comodojo\Cookies\Cookie;

class Authentication extends DispatcherService {

    private static $cookie_name = 'comodojo-auth-token';

    public function setup() {

        $this->expects("POST", array('action'));

        $this->likes("POST", array('user','password'));

    }

    public function post() {

        $action = $this->getParameter("action");

        $user  = $this->getParameter("user");

        $password  = $this->getParameter("password");

        try {

            switch ( strtoupper($action) ) {

                case "LOGIN":

                    $auth = Broker::login($user, $passwork);

                    if ( $auth instanceof UserProfile ) {

                        self::createCookie($auth);

                        $return = array(
                            'success' => true,
                            'data' => $auth->toArray()
                        );

                    } else {

                        $return = array(
                            'success' => false,
                            'data' => 'Invalid credentials'
                        );

                    }

                break;

                case "LOGOUT":

                    Broker::logout();

                    self::deleteCookie();

                    $return = array(
                        'success' => true,
                        'message' => 'Logged out'
                    );

                break;

                default:

                    throw new DispatcherException("Invalid auth action", 400);

                break;

            }

        } catch (DispatcherException $de) {

            throw $de;

        } catch (Exception $e) {

            throw $e;

        }

        return $this->serialize->toJson($return);

    }

    private static function createCookie(UserProfile $profile) {

    }

    private static function deleteCookie() {

        return Cookie::erase(self::$cookie_name);

    }

}
