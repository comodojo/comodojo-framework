<?php namespace Comodojo\Roles;

use \Comodojo\Database\Database;
use \Comodojo\Base\Iterator;
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

class Roles extends Iterator {

    public function getElementByName($name) {

        if (!isset($this->data[$name]))
            return null;

        return Role::load($this->data[$name], $this->dbh);

    }

    public function removeElementByName($name) {

        if (isset($this->data[$name])) {

            Role::load($this->data[$name], $this->dbh)->delete();

            unset($this->data[$name]);

        }

        return $this;

    }

    protected function loadData() {

        $this->data = array();

        $query = "SELECT * FROM comodojo_roles ORDER BY name";

        try {

            $result = $this->dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->loadList($result, 'name');

        return $this;

    }


}