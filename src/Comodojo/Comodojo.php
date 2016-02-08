<?php namespace Comodojo;

use \Comodojo\Users\User;
use \Comodojo\Cookies\CookieManager;
use \Comodojo\Cookies\Cookie;
use \Comodojo\Base\Firestarter;
use \Comodojo\Base\Error as StartupError;
use \Comodojo\Dispatcher\Dispatcher;
use \League\Event\Emitter;
use \Comodojo\Base\CacheHandler;
use \Comodojo\Base\LogHandler;
use \Comodojo\Base\AuditHandler;
use \Comodojo\Exception\AuthenticationException;
use \Comodojo\Exception\DatabaseException;
use \Comodojo\Exception\CookieException;
use \Exception;


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

    private $logger;

    private $audit;

    private $cache;

    private $dispatcher;

    private $events;

    private $startup_exception;

    public function __construct( $configuration = array() ) {

        $this->getStaticConfiguration($configuration);

        $this->events = new Emitter();
        
        $this->logger = LogHandler::create($this->configuration);
        
        $this->audit = AuditHandler::create($this->configuration);

        $this->cache = CacheHandler::create($this->configuration);

        try {

            $this->getDatabase();

            $this->getConfiguration();

            $this->dispatcher = new Dispatcher($this->configuration, $this->events, $this->cache, $this->logger);

            $this->dispatcher->extra()
                ->set( 'database', $this->database() )
                ->set( 'audit', $this->audit() );

            $this->dispatcher->table()->put("/", "ROUTE", "\\Comodojo\\Services\\Landing");

            $this->dispatcher->table()->put("/rpc", "ROUTE", "\\Comodojo\\Services\\Rpc");

            $this->dispatcher->table()->put("/authentication", "ROUTE", "\\Comodojo\\Services\\Authentication");

            $plugins_handler = new Plugins($this->database, $this->configuration);

            $plugins = $plugins_handler->getByFramework('dispatcher');

            foreach ( $plugins as $plugin ) {

                $this->dispatcher->events()->addListener($plugin->getEvent(), $plugin->getCallable());

            }

        } catch (Exception $e) {

            $this->startup_exception = $e;

        }

    }
    
    final public function events() {

        return $this->events;

    }

    final public function logger() {

        return $this->logger;

    }

    final public function audit() {

        return $this->audit;

    }

    final public function cache() {

        return $this->cache;

    }

    final public function dispatcher() {

        return $this->dispatcher;

    }

    public function boot() {

        if ( $this->startup_exception !== null ) {

            return StartupError::raise($e);

        }

        try {

            $user = self::getCurrentUser($this->configuration, $this->database);

        } catch (Exception $e) {

            return StartupError::raise($e);

        }

        try {

            $roles = $user->getRoles();

            $apps = array();

            foreach ($roles as $role ) {

                $apps[] = $role->getApplications();

            }

            foreach ( $apps as $app ) {

                foreach ( $app->getRoutes() as $route ) {

                    $this->dispatcher->table()->put($route->getName(), $route->getType(), $route->getClass(), $route->getParameters());

                }

            }

            $return = $this->dispatcher()->dispatch();

        } catch (Exception $e) {

            return StartupError::raise($e);

        }

        return $return;

    }

    private static function getCurrentUser(Configuration $configuration, EnhancedDatabase $database) {

        $token_name = $configuration->get('auth-token');

        try {

            $token = Cookie::retrieve($token_name);

            $broker = new Broker($database);

            $user = $broker->validate($token);

        } catch (CookieException $ce ) {

            $user = User::loadGuest();

        } catch (AuthenticationException $ae) {

            $user = User::loadGuest();

        } catch (Exception $e) {

            throw $e;

        }

        return $user;

    }

    private function getConfiguration() {
        
        $startup_cache_enabled = $this->configuration->get('startup-cache-enabled');
        
        $startup_cache_ttl = $this->configuration->get('startup-cache-ttl');
        
        if ( $startup_cache_enabled === true ) {
            
            $cached = $this->cache->setNamespace('COMODOJO')->get('startup-configuration');
            
            if ( $cache instanceof Settings ) {
                
                $settings = $cache;
                
            } else {
                
                $settings = new Settings( $this->database() );
                
                $this->cache->set('startup-configuration', $settings, $startup_cache_ttl);
                
            }

        } else {
            
            $settings = new Settings( $this->database() );
            
        }

        foreach ( $settings as $setting => $value ) {

            $this->configuration->set($setting, $value);

        }

    }

}
