<?php namespace Comodojo\Users;

use \Comodojo\Database\Database;
use \Comodojo\Roles\Role;
use \Comodojo\Roles\Manager as RolesManager;
use \Comodojo\Authentication\Authentication;
use \Comodojo\Authentication\Manager as AuthenticationManager;
use \Comodojo\Configuration\PackageApp;
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

class UserProfile implements \Serializable {
	
	private $id             = 0;
	
    private $username       = "";
    
    private $password       = "";
    
    private $displayname    = "";
    
    private $mail           = "";
    
    private $birthdate      = "";
    
    private $gender         = "";
    
    private $enabled        = false;
    
    private $authentication = null;
    
    private $primaryrole    = null;
	
	private $dbh            = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh = $dbh;
		
	}
	
	public function getID() {
		
		return $this->id;
		
	}
	
	public function getUsername() {
		
		return $this->username;
		
	}
	
	public function setUsername($username) {
		
		$this->username = $username;
		
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
	
	public function setAuthentication(Authentication $authentication) {
		
		$this->authentication = $authentication;
		
		return $this;
		
	}
	
	public function getPrimaryRole() {
		
		return $this->primaryrole;
		
	}
	
	public function setPrimaryRole(Role $primaryrole) {
		
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
			
				$manager = new RolesManager($this->dbh);
        		
        		array_push($roles, $manager->getRoleByID($row['id']));
        		
        	}
			
        }
		
		return $roles;
		
	}
	
	public function save() {
		
		if ($this->id == 0) {
			
			$this->create();
			
		} else {
			
			$this->update();
			
		}
        
        return $this;
		
	}
	
	private function create() {
		
		$query = sprintf("INSERT INTO comodojo_users VALUES (0, '%s', '%s', '%s', '%s', '%s', '%s', %d, %s, %s)",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->username),
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
	
	private function update() {
		
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
			mysqli_real_escape_string($this->dbh->getHandler(), $this->username),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->password),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->displayname),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->mail),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->birthdate),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->gender),
			(($this->getEnabled())?1:0),
			(is_null($this->app) || $this->authentication->getID() == 0)?'NULL':$this->authentication->getID(),
			(is_null($this->app) || $this->primaryrole->getID() == 0)?'NULL':$this->primaryrole->getID(),
			$this->settings[$name]['id']
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
        
		$this->id             = 0;
    	$this->username       = "";
    	$this->password       = "";
    	$this->displayname    = "";
    	$this->mail           = "";
    	$this->birthdate      = "";
    	$this->gender         = "";
    	$this->enabled        = false;
    	$this->authentication = null;
    	$this->primaryrole    = null;
		
		return $this;
		
	}
	
    public function getData() {
    	
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
    	
    	if 	(!is_null($this->authentication) && $this->authentication->getID() !== 0)
    		array_push($data, $this->authentication->getID());
    		
    	if 	(!is_null($this->primaryrole) && $this->primaryrole->getID() !== 0)
    		array_push($data, $this->primaryrole->getID());
    	
    	return $data;
        
    }
	
	public static function loadData($dbh, $data) {
		
		$user = new UserProfile($dbh);
        
		$user->id             = $data[0];
    	$user->username       = $data[1];
    	$user->password       = $data[2];
    	$user->displayname    = $data[3];
    	$user->mail           = $data[4];
    	$user->birthdate      = $data[5];
    	$user->gender         = $data[6];
    	$user->enabled        = ($data[7] == 1);
		
		if (!isset($data[8]) && is_numeric($data[8])) {
			
			$manager = new AuthenticationManager($this->dbh);
			
			$user->authentication = $manager->getAuthenticationByID($data[8]);
			
		}
		
		if (!isset($data[9]) && is_numeric($data[9])) {
			
			$manager = new RolesManager($this->dbh);
			
			$user->primaryrole = $manager->getRoleByID($data[9]);
			
		}
        
        return $user;
		
	}
	
	public static function loadUser($id, $dbh) {
		
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
        	
			return self::loadData($dbh, $data);
			
        }
        
        return null;
		
	}
	
    /**
     * The following methods implement the Serializable interface
     */
	
    /**
     * Return the serialized data
     *
     * @return string $serialized
     */
    public function serialize() {
    	
    	$data = $this->getData();
    	
    	return serialize(
            $data
        );
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Manager $this
     */
    public function unserialize($data) {
    	
    	$data = unserialize($data);
        
		$this->id             = $data[0];
    	$this->username       = $data[1];
    	$this->password       = $data[2];
    	$this->displayname    = $data[3];
    	$this->mail           = $data[4];
    	$this->birthdate      = $data[5];
    	$this->gender         = $data[6];
    	$this->enabled        = ($data[7] == 1);
		
		if (!isset($data[8]) && is_numeric($data[8])) {
			
			$manager = new AuthenticationManager($this->dbh);
			
			$this->authentication = $manager->getAuthenticationByID($data[8]);
			
		}
		
		if (!isset($data[9]) && is_numeric($data[9])) {
			
			$manager = new RolesManager($this->dbh);
			
			$this->primaryrole = $manager->getRoleByID($data[9]);
			
		}
        
        return $this;
        
    }
	

}
