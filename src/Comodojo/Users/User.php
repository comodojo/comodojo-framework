<?php namespace Comodojo\Users;

use \Comodojo\Database\EnhancedDatabase;
use \Comodojo\Base\Element;
use \Comodojo\Authentication\Authentication;
use \Comodojo\Roles\Role;
use \Comodojo\Roles\Roles;
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

    private $gender_types = array('M', 'F');
    
    private $password_hash;
    
    protected $displayname;

    protected $mail;

    protected $birthdate;

    protected $gender;

    protected $enabled = false;

    protected $authentication;

    protected $primaryrole;
    
    protected $roles;
    
    protected $password;

    public function getUsername() {

        return $this->name;

    }

    public function setUsername($username) {

        $this->name = $username;

        return $this;

    }

    public function getPasswordHash() {

        return $this->password_hash;

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

        $this->mail = filter_var($mail, FILTER_VALIDATE_EMAIL);

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
        
        if ( in_array($gender, $this->gender_types) ) throw new Exception('Invalid gender for user');

        $this->gender = $gender;

        return $this;

    }

    public function getEnabled() {

        return $this->enabled;

    }

    public function setEnabled($enabled) {

        $this->enabled = filter_var($enabled, FILTER_VALIDATE_BOOLEAN);

        return $this;

    }

    public function getAuthentication() {

        return $this->authentication;

    }

    public function setAuthentication($authentication) {
        
        $provider = Authentication::load($this->database, intval($authentication));

        if ( is_null($provider) ) throw new Exception('Invalid authentication provider');

        $this->authentication = $provider;

        return $this;

    }

    public function getPrimaryRole() {

        return $this->primaryrole;

    }

    public function setPrimaryRole($primaryrole) {
        
        $role = Role::load($this->database, intval($primaryrole));

        if ( is_null($role) ) throw new Exception('Invalid role');

        $this->primaryrole = $role;
        
        $this->pushRole($role->getId());

        return $this;

    }
    
    public function getRoles() {
        
        return $this->roles;
        
    }
    
    public function addRole($role) {
        
        $new_role = Role::load($this->database, intval($role));

        if ( is_null($new_role) ) throw new Exception('Invalid role');

        $this->pushRole($new_role->getId());

        return $this;
        
    }
    
    public function changePassword($old_password, $new_password) {
        
        try {
            
            $result = $this->authentication->getProvider($this)->chpasswd($old_password, $new_password);
            
        } catch (Exception $e) {
            
            throw $e;
            
        }
        
        return $result;
        
    }

    public static function load(EnhancedDatabase $database, $id) {

        try {

            $result = Model::load($datanase, $id);
            
        } catch (DatabaseException $de) {

            throw $de;

        }

        if ($result->getLength() > 0) {

            $data = $result->getData();

            $data = array_values($data[0]);

            $user = new User($dbh);

            $user->setData($data);

        } else {
            
            throw new Exception("Unable to load user");
            
        }
        
        return $user;

    }

    protected function getData() {

        $data = array(
            $this->id,
            $this->name,
            $this->password_hash,
            $this->displayname,
            $this->mail,
            $this->birthdate,
            $this->gender,
            $this->enabled
        );

        if ( $this->authentication instanceof Authentication ) {
            
            array_push($data, $this->authentication->getId());
            
        }
        
        if ( $this->primaryrole instanceof Role ) {
            
            array_push($data, $this->primaryrole->getId());
            
        }
        
        return $data;

    }

    protected function setData($data) {

        $this->id = $data[0];
        $this->name = $data[1];
        $this->password_hash = $data[2];
        $this->displayname = $data[3];
        $this->mail = $data[4];
        $this->birthdate = $data[5];
        $this->gender = $data[6];
        $this->enabled = ($data[7] == 1);
        $this->setAuthentication($data[8]);
        $this->setPrimaryRole($data[9]);

        $this->loadRoles();

        return $this;

    }

    protected function create() {

        try {

            $result = Model::create(
                $this->database,
                $this->name,
                $this->displayname,
                $this->mail,
                $this->birthdate,
                $this->gender,
                $this->enabled,
                ( $this->authentication instanceof Authentication ) ? $this->authentication->getId() : null,
                ( $this->primaryrole instanceof Role ) ? $this->primaryrole->getId() : null
            );
            
            $this->id = $result->getInsertId();
            
            foreach( $this->roles as $role ) {
                
                $this->pushRole($role->getId());
                
            }
            
            if ( $this->authentication instanceof Authentication ) {
                
                $this->authentication->getProvider($this)->passwd($this->password);
                
            }

        } catch (DatabaseException $de) {

            throw $de;

        }

        return $this;

    }

    protected function update() {
        
        try {

            $result = Model::update(
                $this->database,
                $this->id,
                $this->name,
                $this->displayname,
                $this->mail,
                $this->birthdate,
                $this->gender,
                $this->enabled,
                ( $this->authentication instanceof Authentication ) ? $this->authentication->getId() : null,
                ( $this->primaryrole instanceof Role ) ? $this->primaryrole->getId() : null
            );
            
            foreach( $this->roles as $role ) {
                
                $this->pushRole($role->getId());
                
            }
            
            if ( 
                ( $this->authentication instanceof Authentication ) &&
                $this->password !== null
            ) {
                
                $this->authentication->getProvider($this)->passwd($this->password);
                
            }

        } catch (DatabaseException $de) {

            throw $de;

        }

        return $this;

    }

    public function delete() {

        try {

            $result = Model::delete(
                $this->database,
                $this->id
            );

        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->setData(array(0, "", "", "", "", "", "", false));

        return $this;

    }

    private function loadRoles() {
        
        try {

            $roles = new Roles($this->database);
            
            $roles->loadByUser($this->id);

        } catch (DatabaseException $de) {

            throw $de;

        }
        
        $this->roles = $roles;
        
    }
    
    private function pushRole($id) {
        
        try {

            $result = Model::pushRole($this->database, $this->id, $id);
            
            $this->loadRoles();

        } catch (DatabaseException $de) {

            throw $de;

        }

        return $result;
        
    }

}
