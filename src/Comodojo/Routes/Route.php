<?php namespace Comodojo\Routes;

use \Comodojo\Database\Database;
use \Comodojo\Base\Element;
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

class Route extends Element {

    protected $type = "";

    protected $cls = "";

    protected $params = array();
    
    protected $app = null;

    public function getType() {

        return $this->type;

    }

    public function setType($type) {

        $this->type = $type;

        return $this;

    }

    public function getApp() {

        return $this->app;

    }

    public function setApp($app) {
        
        if (!empty($app) && is_numeric($app))
            $app = App::load(intval($app), $this->dbh);

        $this->app = $app;

        return $this;

    }

    public function getClass() {

        return $this->cls;

    }

    public function getInstance() {

        $class = $this->cls;

        if (class_exists($class))
            return new $class();

        return null;

    }

    public function setClass($class) {

        $this->cls = $class;

        return $this;

    }

    public function getParameters() {

        return array_keys($this->params);

    }

    public function getParameter($name) {

        return $this->params[$name];

    }

    public function setParameter($name, $value) {

        $this->params[$name] = $value;

        return $this;

    }

    public function unsetParameter($name) {

        if (isset($this->params[$name]))
            unset($this->params[$name]);

        return $this;

    }

    public static function load($id, $dbh) {

        $query = sprintf("SELECT * FROM comodojo_routes WHERE id = %d",
            $id
        );

        try {

            $result = $dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        if ($result->getLength() > 0) {

            $data = $result->getData();

            $data = array_values($data[0]);

            $route = new Route($dbh);

            $route->setData($data);

            return $route;

        }

    }

    protected function getData() {

        $data = array(
            $this->id,
            $this->name,
            $this->type,
            $this->cls,
            json_encode($this->params),
            $this->package
        );
        
        if (!is_null($this->app) && $this->app->getID() !== 0)
            array_push($data, $this->app->getID());
            
        return $data;

    }

    protected function setData($data) {

        $this->id      = intval($data[0]);
        $this->name    = $data[1];
        $this->type    = $data[2];
        $this->cls     = $data[3];
        $this->params  = json_decode($data[4], true);
        $this->package = $data[5];
        $this->setApp($data[6]);

        return $this;

    }

    protected function create() {

        $query = sprintf("INSERT INTO comodojo_routes VALUES (0, '%s', '%s', '%s', '%s', '%s', %s)",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->type),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
            mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->params)),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->package),
            (is_null($this->app) || $this->app->getID() == 0)?'NULL':$this->app->getID()
        );

        try {

            $result = $this->dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->id = $result->getInsertId();

        return $this;

    }

    protected function update() {

        $query = sprintf("UPDATE comodojo_routes SET `route` = '%s', `type` = '%s', `class` = '%s', `parameters` = '%s', `package` = '%s' , `application` = %s WHERE id = %d",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->type),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
            mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->params)),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->package),
            (is_null($this->app) || $this->app->getID() == 0)?'NULL':$this->app->getID(),
            $this->id
        );

        try {

            $this->dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        return $this;

    }

    public function delete() {

        $query = sprintf("DELETE FROM comodojo_routes WHERE id = %d",
            $this->id
        );

        try {

            $this->dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->setData(array(0, "", "", "", "[]", ""));

        return $this;

    }

}
