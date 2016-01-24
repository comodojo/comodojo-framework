<?php namespace Comodojo\Settings;

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

class Setting extends Element {

    protected $value = "";

    public function getValue() {

        return $this->value;

    }

    public function setValue($value) {

        $this->value = $value;

        return $this;

    }

    public static function load($id, $dbh) {

        $query = sprintf("SELECT * FROM comodojo_settings WHERE id = %d",
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

            $setting = new Setting($dbh);

            $setting->setData($data);

            return $setting;

        }

    }

    protected function getData() {

        return array(
            $this->id,
            $this->name,
            $this->value,
            $this->package
        );

    }

    protected function setData($data) {

        $this->id      = intval($data[0]);
        $this->name    = $data[1];
        $this->value   = $data[2];
        $this->package = $data[3];

        return $this;

    }

    protected function create() {

        $query = sprintf("INSERT INTO comodojo_settings VALUES (0, '%s', '%s', '%s')",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->value),
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

        $query = sprintf("UPDATE comodojo_settings SET name = '%s', value = '%s', package = '%s' WHERE id = %d",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->value),
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

        $query = sprintf("DELETE FROM comodojo_settings WHERE id = %d",
            $this->id
        );

        try {

            $this->dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->setData(array(0, "", "", ""));

        return $this;

    }

}
