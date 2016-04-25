<?php namespace Comodojo\Services;

use \Comodojo\Authentication\Broker;
use \Comodojo\Authentication\Token;
use \Comodojo\Users\Users;
use \Comodojo\Dispatcher\Service\AbstractService;
use \Comodojo\Cookies\Cookie;

class Authentication extends AbstractService {

    private static $cookie_name = 'comodojo-auth-token';

    public function post() {

        $action = $this->getParameter("action");

        $username  = $this->getParameter("user");

        $password  = $this->getParameter("password");

        try {

            $broker = new Broker($this->getDatabase());

            switch ( strtoupper($action) ) {

                case "LOGIN":

                    try {

                        $token = $broker->authenticate($username, $password);

                        $this->createCookie($token);

                    } catch (AuthenticationException $ae) {

                        return json_encode(array(
                            'success' => false,
                            'data' => $ae->getMessage()
                        ));

                    } catch (Exception $e) {

                        throw $e;

                    }

                break;

                case "LOGOUT":

                    $broker->release($username);

                    $this->deleteCookie($token);

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
