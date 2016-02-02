<?php namespace Comodojo\Authentication;

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

class Authentication extends Element {

    protected $description = "";

    protected $classname = "";

    protected $parameters = array();

    public function getDescription() {

        return $this->description;

    }

    public function setDescription($description) {

        $this->description = $description;

        return $this;

    }

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

        if ( isset($this->parameters[$name]) ) unset($this->parameters[$name]);

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

            $auth = new Authentication($dbh);

            $auth->setData($data);

        } else {
            
            throw new Exception("Unable to load role");
            
        }
        
        return $auth;

    }

    protected function getData() {

        return array(
            $this->id,
            $this->name,
            $this->description,
            $this->classname,
            json_encode($this->parameters),
            $this->package
        );

    }

    protected function setData($data) {

        $auth->id = $data[0];
        $auth->name = $data[1];
        $auth->description = $data[2];
        $auth->classname = $data[3];
        $auth->parameters = json_decode($data[4]);
        $auth->package = $data[5];

        return $this;

    }

    protected function create() {

        try {

            $result = Model::create(
                $this->database,
                $this->name,
                $this->description,
                $this->classname,
                $this->parameters,
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
                $this->description,
                $this->classname,
                $this->parameters,
                $this->package
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

        $this->setData(array(0, "", "", "", [], ""));

        return $this;

    }


}
