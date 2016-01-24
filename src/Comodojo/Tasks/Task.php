<?php namespace Comodojo\Tasks;

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

class Task extends Element {

    protected $cls = "";

    protected $desc = "";

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

    public function getDescription() {

        return $this->description;

    }

    public function setDescription($description) {

        $this->description = $description;

        return $this;

    }

    public static function load($id, $dbh) {

        $query = sprintf("SELECT * FROM comodojo_tasks WHERE id = %d",
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

            $task = new Task($dbh);

            $task->setData($data);

            return $task;

        }

    }

    protected function getData() {

        return array(
            $this->id,
            $this->name,
            $this->cls,
            $this->desc,
            $this->package
        );

    }

    protected function setData($data) {

        $this->id      = intval($data[0]);
        $this->name    = $data[1];
        $this->cls     = $data[2];
        $this->desc    = $data[3];
        $this->package = $data[4];

        return $this;

    }

    protected function create() {

        $query = sprintf("INSERT INTO comodojo_tasks VALUES (0, '%s', '%s', '%s', '%s')",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->desc),
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

        $query = sprintf("UPDATE comodojo_tasks SET name = '%s', class = '%s', description = '%s', package = '%s' WHERE id = %d",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->desc),
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

        $query = sprintf("DELETE FROM comodojo_tasks WHERE id = %d",
            $this->id
        );

        try {

            $this->dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->setData(array(0, "", "", "", ""));

        return $this;

    }

}
