<?php namespace Comodojo\Roles;

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

class Role extends Element {

    protected $desc = "";

    protected $app = null;

    public function getDescription() {

        return $this->desc;

    }

    public function setDescription($desc) {

        $this->desc = $desc;

        return $this;

    }

    public function getLandingApp() {

        return $this->app;

    }

    public function setLandingApp($app) {

        if (is_numeric($app))
            $app = App::load(intval($app), $this->dbh);

        $this->app = $app;

        return $this;

    }

    public function getUsers() {

        $users = array();


        $query = sprintf("SELECT comodojo_users.id as id
        FROM
            comodojo_users,
            comodojo_users_to_roles
        WHERE
            comodojo_users.id = comodojo_users_to_roles.user AND
            comodojo_users_to_roles.role = %d",
            $this->id
        );

        try {

            $result = $dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {

                array_push($users, User::load($row['id'], $this->dbh));

            }

        }

        return $users;

    }

    public function getApplications() {

        $apps = array();


        $query = sprintf("SELECT comodojo_apps.id as id
        FROM
            comodojo_apps,
            comodojo_apps_to_roles
        WHERE
            comodojo_apps.id = comodojo_apps_to_roles.app AND
            comodojo_apps_to_roles.role = %d",
            $this->id
        );

        try {

            $result = $dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {

                array_push($apps, App::load($row['id'], $this->dbh));

            }

        }

        return $apps;

    }

    public static function load($id, $dbh) {

        $query = sprintf("SELECT * FROM comodojo_roles WHERE id = %d",
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

            $role = new Role($dbh);

            $role->setData($data);

            return $role;

        }

    }

    protected function getData() {

        $data = array(
            $this->id,
            $this->name,
            $this->desc
        );

        if     (!is_null($this->app) && $this->app->getID() !== 0)
            array_push($data, $this->app->getID());

        return $data;

    }

    protected function setData($data) {

        $this->id   = $data[0];
        $this->name = $data[1];
        $this->desc = $data[2];

        if (!isset($data[3]) && is_numeric($data[3]))
            $this->app = App::load(intval($data[3]), $this->dbh);

        return $this;

    }

    protected function create() {

        $query = sprintf("INSERT INTO comodojo_roles VALUES (0, '%s', '%s', %s)",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->description),
            mysqli_real_escape_string(
                $this->dbh->getHandler(),
                (is_null($this->app) || $this->app->getID() == 0)?'NULL':$this->app->getID()
            )
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

        $query = sprintf("UPDATE comodojo_roles SET name = '%s', value = '%s', landingapp = %s WHERE id = %d",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->description),
            mysqli_real_escape_string(
                $this->dbh->getHandler(),
                (is_null($this->app) || $this->app->getID() == 0)?'NULL':$this->app->getID()
            ),
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

        $query = sprintf("DELETE FROM comodojo_roles WHERE id = %d",
            $this->id
        );

        try {

            $this->dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->setData(array(0, "", ""));

        return $this;

    }


}
