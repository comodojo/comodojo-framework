<?php namespace Comodojo\Authentication;

use \Comodojo\Database\Database;
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

    protected $desc = "";

    protected $cls = "";

    protected $param = array();

    public function getDescription() {

        return $this->desc;

    }

    public function setDescription($desc) {

        $this->desc = $desc;

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

        $query = sprintf("SELECT * FROM comodojo_authentication WHERE id = %d",
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

            $auth = new Authentication($dbh);

            $auth->setData($data);

            return $auth;

        }

    }

    protected function getData() {

        return array(
            $this->id,
            $this->name,
            $this->desc,
            $this->cls,
            json_encode($this->param),
            $this->package
        );

    }

    protected function setData($data) {

        $auth->id      = $data[0];
        $auth->name    = $data[1];
        $auth->desc    = $data[2];
        $auth->cls     = $data[3];
        $auth->param   = json_decode($data[4]);
        $auth->package = $data[5];

        return $this;

    }

    protected function create() {

        $query = sprintf("INSERT INTO comodojo_authentication VALUES (0, '%s', '%s', '%s', '%s', '%s')",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->desc),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
            mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->param)),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->package)
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

        $query = sprintf("UPDATE comodojo_authentication SET name = '%s', description = '%s', class = '%s', parameters = '%s', package = '%s' WHERE id = %d",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->desc),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
            mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->param)),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->package),
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

        $query = sprintf("DELETE FROM comodojo_authentication WHERE id = %d",
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
