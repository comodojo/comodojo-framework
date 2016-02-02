<?php namespace Comodojo\Plugins;

use \Comodojo\Database\EnhancedDatabase;
use \Comodojo\Base\Element;
use \Comodojo\Exception\DatabaseException;
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

class Plugin extends Element {

    protected $classname = "";

    protected $method = "";

    protected $event = "";

    protected $framework = "";

    public function getClass() {

        return $this->classname;

    }

    public function getInstance() {

        $class = $this->classname;

        if ( class_exists($class) ) return new $class();

        return null;

    }

    public function setClass($class) {

        $this->classname = $class;

        return $this;

    }

    public function getMethod() {

        return $this->method;

    }

    public function setMethod($method) {

        $this->method = $method;

        return $this;

    }

    public function execute() {

        $obj = $this->getInstance();

        $params = func_get_args();

        if ( !is_null($obj) ) {

            if ( !empty($this->method) && method_exists($obj, $this->method) ) {

                return call_user_func(array($obj, $this->method), $params);

            }

        }

        return null;

    }

    public function getEvent() {

        return $this->event;

    }

    public function setEvent($name) {

        $this->event = $name;

        return $this;

    }

    public function getFramework() {

        return $this->framework;

    }

    public function setFramework($framework) {

        $this->framework = $framework;

        return $this;

    }

    public static function load(EnhancedDatabase $database, $id) {

        try {

            $result = Model::load($database, $id);

        } catch (DatabaseException $de) {

            throw $de;

        }

        if ($result->getLength() > 0) {

            $data = $result->getData();

            $data = array_values($data[0]);

            $plugin = new Plugin($dbh);

            $plugin->setData($data);

        } else {
            
            throw new Exception("Unable to load role");
            
        }
        
        return $plugin;

    }

    protected function getData() {

        return array(
            $this->id,
            $this->name,
            $this->classname,
            $this->method,
            $this->event,
            $this->framework,
            $this->package
        );

    }

    protected function setData($data) {

        $this->id = intval($data[0]);
        $this->name = $data[1];
        $this->classname = $data[2];
        $this->method = $data[3];
        $this->event = $data[4];
        $this->framework = $data[5];
        $this->package = $data[6];

        return $this;

    }

    protected function create() {

        try {

            $result = Model::create(
                $this->database,
                $this->name,
                $this->classname,
                $this->method,
                $this->event,
                $this->framework,
                $this->package
            );

        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->id = $result->getInsertId();

        return $this;

    }

    protected function update() {

        try {

            $result = Model::update(
                $this->database,
                $this->id,
                $this->name,
                $this->classname,
                $this->method,
                $this->event,
                $this->framework,
                $this->package
            );

        } catch (DatabaseException $de) {

            throw $de;

        }

        return $this;

    }

    public function delete() {

        try {

            $result = Model::update($this->database, $this->id);

        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->setData(array(0, "", "", "", "", "", ""));

        return $this;

    }

}
