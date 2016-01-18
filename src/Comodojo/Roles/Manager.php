<?php namespace Comodojo\Roles;

use \Comodojo\Database\Database;
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

class Manager implements \Iterator, \ArrayAccess, \Countable, \Serializable {
	
	private $roles    = array();
	
	private $current  = 0;
	
	private $dbh      = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh = $dbh;
		
		$this->loadRoles();
		
	}
	
	public function getRoleByName($name) {
		
		foreach ($this->roles as $role) {
			if ($role->getID() != 0 && $role->getName() == $name) return $role;
		}
		
		return null;
		
	}
	
	public function getRoleByID($id) {
		
		foreach ($this->roles as $role) {
			if ($role->getID() != 0 && $role->getID() == $id) return $role;
		}
		
		return null;
		
	}
	
	public function addRole($name, $description = "") {
		
		$role = new Role($this->dbh);
		
		$role->setName($name)->setDescription($description)->save();
		
		array_push($this->roles, $role);
		
		return $role;
		
	}
	
	public function getRoles() {
		
		$roles = array();
		
		foreach ($this->roles as $role) {
			if ($role->getID() != 0) 
				array_push($roles, $role->getName());
		}
		
		sort($roles);
		
		return $roles;
		
	}
	
	private function loadRoles() {
		
		$this->settings = array();
		
		$query = "SELECT * FROM comodojo_roles ORDER BY name";
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {
            	
            	array_push($this->roles, Role::loadData($this->dbh, array_values($row)));
            	
            }
        
        }
        
        return $this;
		
	}
	
    /**
     * The following methods implement the Iterator interface
     */
	
    /**
     * Reset the iterator
     *
     * @return Settings $this
     */
    public function rewind() {
			
		$this->current  = 0;
    	
    	return $this;
        
    }
	
    /**
     * Return the current role
     *
     * @return Role $value
     */
    public function current() {
    	
    	$roles = $this->getRoles();
        
    	return $this->getRolesByName($roles[$this->current]);
        
    }
	
    /**
     * Return the current role name
     *
     * @return string $name
     */
    public function key() {
    	
    	$roles = $this->getRoles();
    	
    	return $roles[$this->current];
        
    }
	
    /**
     * Fetch the iterator
     *
     * @return Settings $this
     */
    public function next() {
    
        $this->current++;
    	
    	return $this;
        
    }
	
    /**
     * Check if there's a next value
     *
     * @return boolean $hasNext
     */
    public function valid() {
    	
    	$roles = $this->getRoles();
    	
    	return isset($roles[$this->current]);
        
    }
	
    /**
     * The following methods implement the ArrayAccess interface
     */
	
    /**
     * Check if an offset exists
     *
     * @param string $name
     *
     * @return boolean $hasRole
     */
    public function offsetExists($name) {
    	
    	$role = $this->getRoleByName($name);
    	
    	return !is_null($role);
        
    }
	
    /**
     * Get a role
     *
     * @param string $name
     *
     * @return Role $role
     */
    public function offsetGet($name) {
    	
        return $this->getRoleByName($name);
        
    }
	
    /**
     * Set a role
     *
     * @param string $name
     * @param Role   $value
     *
     * @return Manager $this
     */
    public function offsetSet($name, &$value) {
    	
    	$role = $this->getRoleByName($name);
    	
    	if (!is_null($role)) {
    		
    		$value->setName($name);
    		
    		$role->setName($value->getName())
    			->setDescription($value->getDescription())
    			->setPackage($value->getPackage());
    		
    	} else {
    		
    		$role = $this->addRole($value->getName(), $value->getDescription())
    			->setPackage($value->getPackage());
    		
    	}
    	
        $value = $role->save();
        
        return $this;
        
    }
	
    /**
     * Remove a setting
     *
     * @param string $name
     *
     * @return Manager $this
     */
    public function offsetUnset($name) {
    	
    	$role = $this->getRoleByName($name);
    	
    	if (!is_null($role)) {
    		
    		$role->delete();
    		
    	}
    	
        return $this;
        
    }
	
    /**
     * The following methods implement the Countable interface
     */
	
    /**
     * Return the amount of roles loaded
     *
     * @return int $count
     */
    public function count() {
    	
    	$roles = $this->getRoles();
    	
    	return count($roles);
        
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
    	
    	$data = array();
    	
		foreach ($this->roles as $role) {
			if ($role->getID() != 0) array_push($data, serialize($role));
		}
    	
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
    	
    	foreach ($data as $role) {
    		
    		$role = unserialize($role);
    		
    		array_push($this->roles, Role::loadData($this->dbh, $role));
    		
    	}
        
        return $this;
        
    }
	

}
