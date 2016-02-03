<?php namespace Comodojo\Routes;

use \Comodojo\Database\EnhancedDatabase;
use \Comodojo\Base\Element;
use \Comodojo\Applications\Application;
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

class Route extends Element {

    protected $type = "";

    protected $cls = "";

    protected $parameters = array();
    
    protected $application = null;

    public function getType() {

        return $this->type;

    }

    public function setType($type) {

        $this->type = $type;

        return $this;

    }

    public function getApplication() {

        return $this->application;

    }

    public function setApplication($app) {
        
        if ( !empty($app) && is_numeric($app) ) {
            
            $app = Application::load($this->database, intval($app));
            
        }

        $this->application = $app;

        return $this;

    }

    public function getClass() {

        return $this->cls;

    }

    public function getInstance() {

        $class = $this->cls;

        if ( class_exists($class) ) {
            
            return new $class();
            
        }

        return null;

    }

    public function setClass($class) {

        $this->cls = $class;

        return $this;

    }

    public function getParameters() {

        return array_keys($this->parameters);

    }

    public function getParameter($name) {

        return $this->parameters[$name];

    }

    public function setParameter($name, $value) {

        $this->parameters[$name] = $value;

        return $this;

    }

    public function unsetParameter($name) {

        if ( isset($this->parameters[$name]) ) {
         
            unset($this->parameters[$name]);
            
        }

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

            $route = new Route($dbh);

            $route->setData($data);

        } else {
            
            throw new Exception("Unable to load task");
            
        }
        
        return $route;

    }

    protected function getData() {

        $data = array(
            $this->id,
            $this->name,
            $this->type,
            $this->cls,
            json_encode($this->parameters),
            $this->package
        );
        
        if (!is_null($this->application) && $this->application->getId() !== 0) {
            
            array_push($data, $this->application->getId());
            
        }
        
        return $data;

    }

    protected function setData($data) {

        $this->id = intval($data[0]);
        $this->name = $data[1];
        $this->type = $data[2];
        $this->cls = $data[3];
        $this->params = json_decode($data[4], true);
        $this->package = $data[5];
        $this->setApplication($data[6]);

        return $this;

    }

    protected function create() {

        try {

            $result = Model::create(
                $this->database,
                $this->name,
                $this->type,
                $this->cls,
                json_encode($this->parameters),
                $this->package,
                ( is_null($this->application) || $this->application->getId() == 0 ) ? null : $this->application->getId()
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
                $this->type,
                $this->cls,
                json_encode($this->parameters),
                $this->package,
                ( is_null($this->application) || $this->application->getId() == 0 ) ? null : $this->application->getId()
            );

        } catch (DatabaseException $de) {

            throw $de;

        }

        return $this;

    }

    public function delete() {

        try {

            $result = Model::delete($this->database, $this->id);

        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->setData(array(0, "", "", "", [], "", null));

        return $this;

    }

}
