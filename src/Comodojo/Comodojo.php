<?php namespace Comodojo;

use \Comodojo\Cookies\CookieManager;
use \Comodojo\Cookies\Cookie;
use \Comodojo\Dispatcher\Dispatcher;
use \Comodojo\Dispatcher\Events\EventsManager;
use \Comodojo\Base\FireStarter;
use \Comodojo\Base\CacheHandler;
use \Comodojo\Base\LogHandler;
use \Comodojo\Base\AuditHandler;
use \Comodojo\Plugin\Iterator as PluginIterator;
use \Comodojo\Setting\Iterator as SettingIterator;
use \Comodojo\User\View as UserView;
use \Comodojo\Authentication\Broker;
use \Comodojo\Exception\AuthenticationException;
use \Comodojo\Exception\DatabaseException;
use \Comodojo\Exception\CookieException;
use \Exception;

/**
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

class Comodojo extends FireStarter {

    private $logger;

    private $audit;

    private $cache;

    private $dispatcher;

    private $events;

    private $startup_exception;

    public function __construct( $configuration = array() ) {

        parent::__construct($configuration);

        $this->loadConfiguration();

        $this->events = new EventsManager();

        $this->logger = LogHandler::create($this->configuration);

        $this->audit = AuditHandler::create($this->configuration);

        $this->cache = CacheHandler::create($this->configuration);

        try {

            $plugins_handler = new Plugins($this->database, $this->configuration);

            $this->dispatcher = new Dispatcher($this->configuration, $this->events, $this->cache, $this->logger);

            $this->dispatcher->extra()
                ->set( 'database', $this->database() )
                ->set( 'audit', $this->audit() );

            $this->dispatcher->router()->add("/", "ROUTE", "\\Comodojo\\Services\\Landing");

            $this->dispatcher->router()->add("/rpc", "ROUTE", "\\Comodojo\\Services\\Rpc");

            $this->dispatcher->router()->add("/authentication", "ROUTE", "\\Comodojo\\Services\\Authentication");

            $this->dispatcher->router()->add("/error", "ROUTE", "\\Comodojo\\Services\\Error");

            foreach ( PluginIterator::loadBy(array('framework','=','dispatcher') ) as $plugin) {

                $this->dispatcher->events()
                    ->subscribe($plugin->event, $plugin->class, $plugin->method);

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

        if ( $this->startup_exception instanceof Exception || $this->loadEnvironment() === false ) {

            $this->suchAMonday();

        }

        return $this->dispatcher()->dispatch();

    }

    private function loadEnvironment() {

        try {

            $user = $this->getCurrentUser();

            $roles = $user->getRoles();

            foreach ($roles as $role) {

                $apps = $role->getApplications();

                foreach ( $apps as $app ) {

                    $routes = $app->getRoutes();

                    foreach ( $routes as $route ) {

                        $this->dispatcher->router()->add($route->get('name'), $route->get('type'), $route->get('class'), $route->get('parameters'));

                    }

                }

            }

        } catch (Exception $e) {

            $this->startup_exception = $e;

            return false;

        }

        return true;

    }

    private function getCurrentUser() {

        $token_name = $this->configuration->get('auth-token');

        try {

            $token = Cookie::retrieve($token_name);

            $broker = new Broker($database);

            $user = $broker->validate($token);

        } catch (CookieException $ce ) {

            $user = $this->loadGuestUser();

        } catch (AuthenticationException $ae) {

            $user = $this->loadGuestUser();

        } catch (Exception $e) {

            throw $e;

        }

        return $user;

    }

    private function loadConfiguration() {

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

    private function loadGuestUser() {

        $user = new UserView($this->configuration, $this->database);

        $user->load(2);

        return $user;

    }

}
