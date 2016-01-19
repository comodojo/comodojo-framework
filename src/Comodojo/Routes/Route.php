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

class Route implements \Serializable {
	
	private $id      = 0;
	
	private $route   = "";
	
	private $type    = "";
	
	private $cls     = "";
	
	private $params  = array();
	
	private $package = "";
	
	private $dbh  = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh  = $dbh;
		
	}
	
	public function getID() {
		
		return $this->id;
		
	}
	
	public function getRouteName() {
		
		return $this->route;
		
	}
	
	public function setRouteName($route) {
		
		$this->route = $route;
		
		return $this;
		
	}
	
	public function getType() {
		
		return $this->type;
		
	}
	
	public function setType($type) {
		
		$this->type = $type;
		
		return $this;
		
	}
	
	public function getClass() {
		
		return $this->cls;
		
	}
	
	public function getInstance() {
		
		$class = $this->cls;
		
		if (class_exists($class))
			return new $class();
		
		return null;
		
	}
	
	public function setClass($class) {
		
		$this->cls = $class;
		
		return $this;
		
	}
	
	public function getParameters() {
		
		return array_keys($this->params);
		
	}
	
	public function getParameter($name) {
		
		return $this->params[$name];
		
	}
	
	public function setParameter($name, $value) {
		
		$this->params[$name] = $value;
		
		return $this;
		
	}
	
	public function unsetParameter($name) {
		
		if (isset($this->params[$name]))
			unset($this->params[$name]);
		
		return $this;
		
	}
	
	public function getPackageName() {
		
		return $this->pack;
		
	}
	
	public function setPackageName($name) {
		
		$this->pack = $name;
		
		return $this;
		
	}
	
	public static function loadRoute($id, $dbh) {
		
		$query = sprintf("SELECT * FROM comodojo_routes WHERE id = %d",
			$id
		);
		       
        try {
            
            $result = $dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {
        
        	$data = $result->getData();
        	
        	$data = $data[0];
        	
        	$route = new Route($dbh);
        	
        	$route->id      = $data['id'];
	    	$route->route   = $data['route'];
	    	$route->type    = $data['type'];
	    	$route->cls     = $data['class'];
	    	$route->params  = json_decode($data['parameters'], true);
	    	$route->package = $data['package'];
        	
        	return $route;
        	
        }
		
	}
	
	public function delete() {
		
		$query = sprintf("DELETE FROM comodojo_routes WHERE id = %d",
			$this->id
		);
		       
        try {
            
            $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        $this->id      = 0;
    	$this->route   = "";
    	$this->type    = "";
    	$this->cls     = "";
    	$this->params  = array();
    	$this->package = "";
		
		return $this;
		
	}
	
	public function save() {
		
		if ($this->id == 0) {
			
			$this->createRoute();
			
		} else {
			
			$this->updateRoute($name);
			
		}
		
		return $this;
		
	}
	
	private function createRoute() {
		
		$query = sprintf("INSERT INTO comodojo_routes VALUES (0, '%s', '%s', '%s', '%s', '%s')",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->route),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->type),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
			mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->params)),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->package)
		);
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        $this->id = $result->getInsertId();
        
        return $this;
		
	}
	
	private function updateRoute() {
		
		$query = sprintf("UPDATE comodojo_routes SET `route` = '%s', `type` = '%s', `class` = '%s', `parameters` = '%s', `package` = '%s' WHERE id = %d",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->route),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->type),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
			mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->params)),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->package),
			$this->id
		);
		       
        try {
            
            $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        return $this;
		
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
    	
    	return serialize(array(
            $this->id,
	    	$this->route,
	    	$this->type,
	    	$this->cls,
	    	json_encode($this->params),
	    	$this->package
        ));
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Routes $this
     */
    public function unserialize($data) {
    	
    	$routeData = unserialize($data);
    	
    	$this->id      = intval($routeData[0]);
    	$this->route   = $data[1];
    	$this->type    = $data[2];
    	$this->cls     = $data[3];
    	$this->params  = json_decode($data[4], true);
    	$this->package = $data[5];
        
        return $this;
        
    }

}
