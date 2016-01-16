<?php namespace Comodojo\Authentication;

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
	
	private $auths    = array();
	
	private $current  = 0;
	
	private $dbh      = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh = $dbh;
		
		$this->loadAuthentications();
		
	}
	
	public function getAuthenticationByName($name) {
		
		foreach ($this->auths as $auth) {
			if ($auth->getID() != 0 && $auth->getName() == $name) return $auth;
		}
		
		return null;
		
	}
	
	public function getAuthenticationByID($id) {
		
		foreach ($this->auths as $auth) {
			if ($auth->getID() != 0 && $auth->getID() == $id) return $auth;
		}
		
		return null;
		
	}
	
	public function addAuthentication($name) {
		
		$auth = new Authentication($this->dbh);
		
		$auth->setName($name)->save();
		
		array_push($this->auths, $auth);
		
		return $auth;
		
	}
	
	public function getAuthentications() {
		
		$auths = array();
		
		foreach ($this->auths as $auth) {
			if ($auth->getID() != 0) 
				array_push($auths, $auth->getName());
		}
		
		sort($auths);
		
		return $auths;
		
	}
	
	private function loadAuthentications() {
		
		$this->settings = array();
		
		$query = "SELECT * FROM comodojo_auths ORDER BY name";
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {
            	
            	array_push($this->auths, Authentication::loadData($this->dbh, array_values($row)));
            	
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
     * Return the current auth
     *
     * @return Authentication $value
     */
    public function current() {
    	
    	$auths = $this->getAuthentications();
        
    	return $this->getAuthenticationsByName($auths[$this->current]);
        
    }
	
    /**
     * Return the current auth name
     *
     * @return string $name
     */
    public function key() {
    	
    	$auths = $this->getAuthentications();
    	
    	return $this->getAuthenticationsByName($auths[$this->current])->getID();
        
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
    	
    	$auths = $this->getAuthentications();
    	
    	return isset($auths[$this->current]);
        
    }
	
    /**
     * The following methods implement the ArrayAccess interface
     */
	
    /**
     * Check if an offset exists
     *
     * @param int $id
     *
     * @return boolean $hasAuthentication
     */
    public function offsetExists($id) {
    	
    	$auth = $this->getAuthenticationByID($id);
    	
    	return !is_null($auth);
        
    }
	
    /**
     * Get a auth
     *
     * @param int $id
     *
     * @return Authentication $auth
     */
    public function offsetGet($id) {
    	
        return $this->getAuthenticationByID($id);
        
    }
	
    /**
     * Set a auth
     *
     * @param int  $id
     * @param Authentication $value
     *
     * @return Manager $this
     */
    public function offsetSet($id, &$value) {
    	
    	$auth = $this->getAuthenticationByName($id);
    	
    	if (!is_null($auth)) {
    		
    		$auth->setName($value->getName())
    			->setDescription($value->getDescription())
    			->setClass($value->getClass())
    			->setParameters($value->getParameters());
    		
    	} else {
    		
    		$auth = $this->addAuthentication($value->getName())
    			->setDescription($value->getDescription())
    			->setClass($value->getClass())
    			->setParameters($value->getParameters());
    		
    	}
    	
        $value = $auth;
        
        return $this;
        
    }
	
    /**
     * Remove a setting
     *
     * @param int  $id
     *
     * @return Manager $this
     */
    public function offsetUnset($id) {
    	
    	$auth = $this->getAuthenticationByName($id);
    	
    	if (!is_null($auth)) {
    		
    		$auth->delete();
    		
    	}
    	
        return $this;
        
    }
	
    /**
     * The following methods implement the Countable interface
     */
	
    /**
     * Return the amount of auths loaded
     *
     * @return int $count
     */
    public function count() {
    	
    	$auths = $this->getAuthentications();
    	
    	return count($auths);
        
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
    	
		foreach ($this->auths as $auth) {
			if ($auth->getID() != 0) array_push($data, serialize($auth));
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
    	
    	foreach ($data as $auth) {
    		
    		$auth = unserialize($auth);
    		
    		array_push($this->auths, Authentication::loadData($this->dbh, $auth));
    		
    	}
        
        return $this;
        
    }
	

}
