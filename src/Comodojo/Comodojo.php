<?php namespace Comodojo;

use \Comodojo\Database\Database;
use \Comodojo\Users\User;
use \Comodojo\Cookies\Cookie;
use \Comodojo\Base\Firestarter;
use \Comodojo\Base\Error as StartupError;
use \Comodojo\Settings\Settings;
use \Comodojo\Dispatcher\Dispatcher;
use \Comodojo\Dispatcher\Routes\RoutingTable;
use \Comodojo\Exception\AuthenticationException;
use \Comodojo\Exception\DatabaseException;
use \Comodojo\Exception\CookieException;
use \League\Event\Emitter;


/**
 *
 *
 * @package     Comodojo Framework
 * @author      Marco Giovinazzi <marco.giovinazzi@comodojo.org>
 * @author      Marco Castiello <marco.castiello@gmail.com>
 * @license     GPL-3.0+
 *
 * LICENSE:
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

class Comodojo {

    use Firestarter;

    private $configuration;
    
    private $logger;
    
    private $cache;
    
    private $dispatcher;
    
    private $events;
    
    private $startup_exception;
    
    public function __construct() {
        
        $this->events = new Emitter();
        
        try {
            
            $this->database = self::getDatabase();
            
            $this->cache = CacheHandler::create();
            
            $this->logger = LogHandler::create();
            
            $this->configuration = $this->initEnvironment();
            
        } catch (Exception $e) {
            
            $this->startup_exception = $e;
            
        }
        
    }
    
    public function logger() {
        
        return $this->logger;
        
    }
    
    public function cache() {
        
        return $this->cache;
        
    }
    
    public function dispatcher() {
        
        return $this->dispatcher;
        
    }
    
    public function boot() {
        
        if ( $this->startup_exception !== null ) {
            
            return StartupError::raise($e);
            
        }
        
        try {
            
            $token = Cookie::retrieve('comodojo-auth-token');
            
            $broker = new Broker($this->dbh);
            
            $user = $broker->validate($token);
            
        } catch (CookieException $ce ) {
            
            $user = User::loadGuest();
            
        } catch (AuthenticationException $ae) {
            
            $user = User::loadGuest();
            
        } catch (Exception $e) {
            
            return StartupError::raise($e);
            
        }
        
        try {
            
            $roles = $user->getRoles();
            
            $apps = array();
            
            foreach ($roles as $role ) {
                
                $apps[] = $role->getApplications();
                
            }
            
            $this->dispatcher = new Dispatcher($this->events, $this->cache, $this->logger);
            
            $this->dispatcher->extra()
                ->set('database', $this->database)
                ->set('configuration', $this->configuration);
            
            $this->dispatcher->router->add("/", "ROUTE", "\\Comodojo\\Services\\Landing");
            
            $this->dispatcher->router->add("/rpc", "ROUTE", "\\Comodojo\\Services\\Rpc");
            
            $this->dispatcher->router->add("/authentication", "ROUTE", "\\Comodojo\\Services\\Authentication");
            
            foreach ( $apps as $app ) {
                
                foreach ( $app->getRoutes() as $route ) {
                 
                    $this->dispatcher->router->add($route->getName(), $route->getType(), $route->getClass(), $route->getParameters());
                    
                }
                
            }
            
            $plugins = new Plugins($this->database);
            
            $plugins = $plugins->getByFramework('dispatcher');
            
            foreach ( $plugins as $plugin ) {
                
                $this->dispatcher->events()->addListener($plugin->getEvent(), $plugin->getCallable());
                
            }
            
        } catch (Exception $e) {
            
            return StartupError::raise($e);
            
        }
        
        return $this->dispatcher()->dispatch();
        
    }

    private function initEnvironment() {
        
        try {
        
            $settings = new Settings($this->database);
            
        } catch (DatabaseException $de) {
            
            throw $de;
            
        }
        
        foreach ($settings as $name => $value) {
            
            if ( !defined($name) ) {
                
                define($name, $value);
                
            }
            
        }
        
    }

}
