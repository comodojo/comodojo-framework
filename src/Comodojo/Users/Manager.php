<?php namespace Comodojo\Users;

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
	
	private $users    = array();
	
	private $current  = 0;
	
	private $dbh      = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh = $dbh;
		
		$this->loadUsers();
		
	}
	
	public function getUserByUsername($name) {
		
		if (isset($this->users[$name])) {
			
			return UserProfile::loadUser($this->users[$name], $this->dbh);
			
		}
		
		return null;
		
	}
	
	public function getUserByID($id) {
		
		$ids = array_values($this->users);
		
		if (in_array($id, $ids)) {
			
			return UserProfile::loadUser($id, $this->dbh);
			
		}
		
		return null;
		
	}
	
	public function getUsers() {
		
		return array_keys($this->users);
		
	}
	
	private function loadUsers() {
		
		$this->settings = array();
		
		$query = "SELECT * FROM comodojo_users ORDER BY name";
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {
            	
            	$this->users[$row['username']] = $row['id'];
            	
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
     * Return the current user
     *
     * @return User $value
     */
    public function current() {
    	
    	$users = $this->getUsers();
        
    	return $this->getUsersByUsername($users[$this->current]);
        
    }
	
    /**
     * Return the current user name
     *
     * @return string $name
     */
    public function key() {
    	
    	$users = $this->getUsers();
    	
    	return $users[$this->current];
        
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
    	
    	$users = $this->getUsers();
    	
    	return isset($users[$this->current]);
        
    }
	
    /**
     * The following methods implement the ArrayAccess interface
     */
	
    /**
     * Check if an offset exists
     *
     * @param string $name
     *
     * @return boolean $hasUser
     */
    public function offsetExists($name) {
		
		return isset($this->users[$name]);
        
    }
	
    /**
     * Get a user
     *
     * @param string $name
     *
     * @return User $user
     */
    public function offsetGet($name) {
    	
        return $this->getUserByUsername($name);
        
    }
	
    /**
     * Set a user
     *
     * @param string $name
     * @param User   $value
     *
     * @return Manager $this
     */
    public function offsetSet($name, &$value) {
    	
    	$user = $this->getUserByUsername($name);
    	
    	if (!is_null($user)) {
    		
    		$id = $user->getID();
    		
    		$data = $value->getData();
    		
    		$data[0] = $id;
    		
    		$user = UserProfile::loadData($this->dbh, $data)->save();
    		
    	} else {
    		
    		$user = $value->save();
    		
    		$this->users[$name] = $value->getID();
    		
    	}
    	
        $value = $user;
        
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
    	
    	$user = $this->getUserByUsername($name);
    	
    	if (!is_null($user)) {
    		
    		$user->delete();
    		
    		unset($this->users[$name]);
    		
    	}
    	
        return $this;
        
    }
	
    /**
     * The following methods implement the Countable interface
     */
	
    /**
     * Return the amount of users loaded
     *
     * @return int $count
     */
    public function count() {
    	
    	$users = $this->getUsers();
    	
    	return count($users);
        
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
    	
		foreach ($this->users as $user) {
			if ($user->getID() != 0) array_push($data, serialize($user));
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
    	
    	foreach ($data as $user) {
    		
    		$user = unserialize($user);
    		
    		array_push($this->users, User::loadData($this->dbh, $user));
    		
    	}
        
        return $this;
        
    }
	

}
