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

class Routes implements \Iterator, \ArrayAccess, \Countable, \Serializable {
	
	private $routes   = array();
	
	private $current  = 0;
	
	private $dbh      = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh  = $dbh;
		
		$this->loadRoutes();
		
	}
	
	public function getRoute($name) {
		
		if (!isset($this->routes[$name]))
			return null;
			
		return Route::loadRoute($this->routes[$name], $this->dbh);
		
	}
	
	public function getRoutes() {
		
		return array_keys($this->routes);
		
	}
	
	public function removeRoute($name) {
		
		if (isset($this->routes[$name])) {
			
			unset($this->routes[$name]);
			
			Route::loadRoute($this->routes[$name], $this->dbh)->delete();
			
		}
		
		return $this;
		
	}
	
	private function loadRoutes() {
		
		$this->routes = array();
		
		$query = "SELECT * FROM comodojo_routes ORDER BY route";
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {
            
                $this->routes[$row['route']] = intval($row['id']);
            
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
     * @return Routes $this
     */
    public function rewind() {
			
		$this->current  = 0;
    	
    	return $this;
        
    }
	
    /**
     * Return the current route description
     *
     * @return string $description
     */
    public function current() {
    	
    	$routes = $this->getRoutes();
        
    	return $this->getRoute($routes[$this->current]);
        
    }
	
    /**
     * Return the current route name
     *
     * @return string $name
     */
    public function key() {
    	
    	$routes = $this->getRoutes();
    	
    	return $routes[$this->current];
        
    }
	
    /**
     * Fetch the iterator
     *
     * @return Routes $this
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
    	
    	$routes = $this->getRoutes();
    	
    	return isset($routes[$this->current]);
        
    }
	
    /**
     * The following methods implement the ArrayAccess interface
     */
	
    /**
     * Check if an offset exists
     *
     * @param string $name
     *
     * @return boolean $hasRoute
     */
    public function offsetExists($name) {
    	
    	return isset($this->routes[$name]);
        
    }
	
    /**
     * Get a route description
     *
     * @param string $name
     *
     * @return string $description
     */
    public function offsetGet($name) {
    	
        return $this->getRoute($name);
        
    }
	
    /**
     * Set a route
     *
     * @param string $name
     * @param Route $route
     *
     * @return Routes $this
     */
    public function offsetSet($name, $route) {
    	
    	$route->setName($name)->save();
    	
    	$this->routes[$name] = $route->getID();
        
        return $this;
        
    }
	
    /**
     * Remove a route
     *
     * @param string $name
     *
     * @return Routes $this
     */
    public function offsetUnset($name) {
        
        return $this->removeRoute($name);
        
    }
	
    /**
     * The following methods implement the Countable interface
     */
	
    /**
     * Return the amount of routes loaded
     *
     * @return int $count
     */
    public function count() {
    	
    	$routes = $this->getRoutes();
    	
    	return count($routes);
        
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
            $this->routes
        );
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Routes $this
     */
    public function unserialize($data) {
    	
    	$this->routes = unserialize($data);
        
        return $this;
        
    }

}
