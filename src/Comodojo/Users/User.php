<?php namespace Comodojo\Users;

use \Comodojo\Authentication\Authentication;
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

class User extends Element {

    protected $password = "";

    protected $displayname = "";

    protected $mail = "";

    protected $birthdate = "";

    protected $gender = "";

    protected $enabled = false;

    protected $authentication;

    protected $primaryrole;

    public function getUsername() {

        return $this->name;

    }

    public function setUsername($username) {

        $this->name = $username;

        return $this;

    }

    public function getPassword() {

        return $this->password;

    }

    public function setPassword($password) {

        $this->password = $password;

        return $this;

    }

    public function getDisplayName() {

        return $this->displayname;

    }

    public function setDisplayName($displayname) {

        $this->displayname = $displayname;

        return $this;

    }

    public function getMail() {

        return $this->mail;

    }

    public function setMail($mail) {

        $this->mail = $mail;

        return $this;

    }

    public function getBirthDate() {

        return $this->birthdate;

    }

    public function setBirthDate($birthdate) {

        $this->birthdate = $birthdate;

        return $this;

    }

    public function getGender() {

        return $this->gender;

    }

    public function setGender($gender) {

        $this->gender = $gender;

        return $this;

    }

    public function getEnabled() {

        return $this->enabled;

    }

    public function setEnabled($enabled) {

        $this->enabled = $enabled;

        return $this;

    }

    public function getAuthentication() {

        return $this->authentication;

    }

    public function setAuthentication($authentication) {

        if (is_numeric($app))
            $authentication = Authentication::load(intval($authentication), $this->dbh);

        $this->authentication = $authentication;

        return $this;

    }

    public function getPrimaryRole() {

        return $this->primaryrole;

    }

    public function setPrimaryRole($primaryrole) {

        if (is_numeric($app))
            $primaryrole = Role::load(intval($primaryrole), $this->dbh);

        $this->primaryrole = $primaryrole;

        return $this;

    }

    public function getRoles() {

        $roles = array();


        $query = sprintf("SELECT comodojo_roles.id as id
        FROM
            comodojo_roles,
            comodojo_users_to_roles
        WHERE
            comodojo_roles.id = comodojo_users_to_roles.role AND
            comodojo_users_to_roles.user = %d",
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

                array_push($roles, Role::load(intval($row['id']), $this->dbh));

            }

        }

        return $roles;

    }

    public static function load($id, $dbh) {

        $query = sprintf("SELECT * FROM comodojo_users WHERE id = %d",
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

            $user = new User($dbh);

            $user->setData($data);

            return $user;

        }

    }

    protected function getData() {

        $data = array(
            $this->id,
            $this->username,
            $this->password,
            $this->displayname,
            $this->mail,
            $this->birthdate,
            $this->gender,
            ($this->enabled)?1:0
        );

        if     (!is_null($this->authentication) && $this->authentication->getID() !== 0)
            array_push($data, $this->authentication->getID());

        if     (!is_null($this->primaryrole) && $this->primaryrole->getID() !== 0)
            array_push($data, $this->primaryrole->getID());

        return $data;

    }

    protected function setData($data) {

        $this->id             = $data[0];
        $this->name           = $data[1];
        $this->password       = $data[2];
        $this->displayname    = $data[3];
        $this->mail           = $data[4];
        $this->birthdate      = $data[5];
        $this->gender         = $data[6];
        $this->enabled        = ($data[7] == 1);
        $this->setAuthentication($data[8]);
        $this->setPrimaryRole($data[9]);

        return $this;

    }

    protected function create() {

        $query = sprintf("INSERT INTO comodojo_users VALUES (0, '%s', '%s', '%s', '%s', '%s', '%s', %d, %s, %s)",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->password),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->displayname),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->mail),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->birthdate),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->gender),
            (($this->getEnabled())?1:0),
            (is_null($this->app) || $this->authentication->getID() == 0)?'NULL':$this->authentication->getID(),
            (is_null($this->app) || $this->primaryrole->getID() == 0)?'NULL':$this->primaryrole->getID()
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

        $query = sprintf("UPDATE comodojo_users SET
            `username` = '%s',
            `password` = '%s',
            `displayname` = '%s',
            `mail` = '%s',
            `birthdate` = '%s',
            `gender` = '%s',
            `enabled` = %d,
            `authentication` = %s,
            `primaryrole` = %s
        WHERE id = %d",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->password),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->displayname),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->mail),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->birthdate),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->gender),
            (($this->getEnabled())?1:0),
            (is_null($this->app) || $this->authentication->getID() == 0)?'NULL':$this->authentication->getID(),
            (is_null($this->app) || $this->primaryrole->getID() == 0)?'NULL':$this->primaryrole->getID(),
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

        $query = sprintf("DELETE FROM comodojo_users WHERE id = %d",
            $this->id
        );

        try {

            $this->dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }


        $this->setData(array(0, "", "", "", "", "", "", false));

        return $this;

    }

}
