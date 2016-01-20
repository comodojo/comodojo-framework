<?php namespace Comodojo\Configuration;

use \Comodojo\Database\Database;
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

class Rpc implements \Iterator, \ArrayAccess, \Countable, \Serializable {
	
	private $methods  = array();
	
	private $current  = 0;
	
	private $dbh      = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh  = $dbh;
		
		$this->loadRpcMethods();
		
	}
	
	public function getRpcMethod($name) {
		
		if (!isset($this->methods[$name]))
			return null;
			
		return Rpc::loadRpcMethod($this->methods[$name], $this->dbh);
		
	}
	
	public function getRpcMethods() {
		
		return array_keys($this->methods);
		
	}
	
	public function removeRpcMethod($name) {
		
		if (isset($this->methods[$name])) {
			
			unset($this->methods[$name]);
			
			Rpc::loadRpcMethod($this->methods[$name], $this->dbh)->delete();
			
		}
		
		return $this;
		
	}
	
	private function loadRpcMethods() {
		
		$this->methods = array();
		
		$query = "SELECT * FROM comodojo_rpc ORDER BY name";
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {
            
                $this->methods[$row['name']] = intval($row['id']);
            
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
     * @return RpcMethods $this
     */
    public function rewind() {
			
		$this->current  = 0;
    	
    	return $this;
        
    }
	
    /**
     * Return the current method description
     *
     * @return string $description
     */
    public function current() {
    	
    	$methods = $this->getRpcMethods();
        
    	return $this->getRpcMethod($methods[$this->current]);
        
    }
	
    /**
     * Return the current method name
     *
     * @return string $name
     */
    public function key() {
    	
    	$methods = $this->getRpcMethods();
    	
    	return $methods[$this->current];
        
    }
	
    /**
     * Fetch the iterator
     *
     * @return RpcMethods $this
     */
    public function next() {
    
        $this->current++;
    	
    	return $this;
        
    }
	
    /**
     * Check if there's a next description
     *
     * @return boolean $hasNext
     */
    public function valid() {
    	
    	$methods = $this->getRpcMethods();
    	
    	return isset($methods[$this->current]);
        
    }
	
    /**
     * The following methods implement the ArrayAccess interface
     */
	
    /**
     * Check if an offset exists
     *
     * @param string $name
     *
     * @return boolean $hasRpc
     */
    public function offsetExists($name) {
    	
    	return isset($this->methods[$name]);
        
    }
	
    /**
     * Get a method description
     *
     * @param string $name
     *
     * @return string $description
     */
    public function offsetGet($name) {
    	
        return $this->getRpcMethod($name);
        
    }
	
    /**
     * Set a method
     *
     * @param string $name
     * @param Rpc $method
     *
     * @return RpcMethods $this
     */
    public function offsetSet($name, $method) {
    	
    	$method->setName($name)->save();
    	
    	$this->methods[$name] = $method->getID();
        
        return $this;
        
    }
	
    /**
     * Remove a method
     *
     * @param string $name
     *
     * @return RpcMethods $this
     */
    public function offsetUnset($name) {
        
        return $this->removeRpcMethod($name);
        
    }
	
    /**
     * The following methods implement the Countable interface
     */
	
    /**
     * Return the amount of methods loaded
     *
     * @return int $count
     */
    public function count() {
    	
    	$methods = $this->getRpcMethods();
    	
    	return count($methods);
        
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
    	
    	return serialize(
            $this->methods
        );
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return RpcMethods $this
     */
    public function unserialize($data) {
    	
    	$this->methods = unserialize($data);
        
        return $this;
        
    }

}