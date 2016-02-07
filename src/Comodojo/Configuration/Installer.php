<?php namespace Comodojo\Configuration;

use \Comodojo\Base\Firestarter;
use \Comodojo\Dispatcher\Components\Configuration;
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

    use Firestarter;

    private $applications;

    private $authentication;

    private $commands;

    private $plugins;

    private $routes;

    private $rpc;

    private $settings;

    private $tasks;

    private $themes;

    public function __construct( Configuration $configuration ) {

        $this->configuration = $configuration;

        $this->getDatabase();

        $this->applications = new Applications($this->database);

        $this->authentication = new Authentication($this->database);

        $this->commands = new Commands($this->database);

        $this->plugins = new Plugins($this->database);

        $this->routes = new Routes($this->database);

        $this->rpc = new Rpc($this->database);

        $this->settings = new Settings($this->database);

        $this->tasks = new Tasks($this->database);

        $this->themes = new Themes($this->database);

    }

    final public function database() {

        return $this->database;

    }

    final public function configuration() {

        return $this->configuration;

    }

    final public function applications() {

        return $this->applications;

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

}
