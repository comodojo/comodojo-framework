<?php namespace Comodojo\Configuration;

use \Comodojo\Database\Database;
use \Comodojo\Exception\DatabaseException;
use \Comodojo\Exception\ConfigurationException;
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

class Installer {

    private $dbh;

    private $apps;

    private $authentication;

    private $commands;

    private $plugins;

    private $routes;

    private $rpc;

    private $settings;

    private $tasks;

    private $themes;

    public function __construct() {

        $this->dbh = self::loadDatabase();

        $this->apps = new Apps($this->dbh);

        $this->authentication = new Authentication($this->dbh);

        $this->commands = new Commands($this->dbh);

        $this->plugins = new Plugins($this->dbh);

        $this->routes = new Routes($this->dbh);

        $this->rpc = new Rpc($this->dbh);

        $this->settings = new Settings($this->dbh);

        $this->tasks = new Tasks($this->dbh);

        $this->themes = new Themes($this->dbh);

    }

    final public function dbh() {

        return $this->dbh;

    }

    final public function apps() {

        return $this->apps;

    }

    final public function authentication() {

        return $this->authentication;

    }

    final public function commands() {

        return $this->commands;

    }

    final public function plugins() {

        return $this->plugins;

    }

    final public function routes() {

        return $this->routes;

    }

    final public function rpc() {

        return $this->rpc;

    }

    final public function settings() {

        return $this->settings;

    }

    final public function tasks() {

        return $this->tasks;

    }

    final public function themes() {

        return $this->themes;

    }

    /**
     * @todo: complete this method!
     */
     private static function loadDatabase() {

        // init a db instance

    }

}
