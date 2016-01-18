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

class PackageApps implements \Iterator, \ArrayAccess, \Countable, \Serializable {
	
	private $apps     = array();
	
	private $current  = 0;
	
	private $name     = "";
	
	private $dbh      = null;
	
	function __construct($name, Database $dbh) {
		
		$this->name = $name;
		$this->dbh  = $dbh;
		
		$this->loadApps();
		
	}
	
	public function getApp($name) {
		
		if (!isset($this->apps[$name]))
			return null;
			
		return PackageApp::loadApp($this->apps[$name], $this->dbh);
		
	}
	
	public function getApps() {
		
		return array_keys($this->apps);
		
	}
	
	public function getPackageName() {
		
		return $this->name;
		
	}
	
	public function setPackageName($name) {
		
		$this->name = $name;
		
		foreach ($this->getApps() as $appName) {
			
			$app = $this->getApp($appName);
			
			$app->setPackageName($name)->save();
			
		}
		
		return $this;
		
	}
	
	
	
	public function removeApp($name) {
		
		if (isset($this->apps[$name])) {
			
			unset($this->apps[$name]);
			
			PackageApp::loadApp($this->apps[$name], $this->dbh)->delete();
			
		}
		
		return $this;
		
	}
	
	private function loadApps() {
		
		$this->apps = array();
		
		$query = sprintf("SELECT * FROM comodojo_apps WHERE package = '%s' ORDER BY name",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name)
		);
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {
            
                $this->apps[$row['name']] = intval($row['id']);
            
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
     * @return Apps $this
     */
    public function rewind() {
			
		$this->current  = 0;
    	
    	return $this;
        
    }
	
    /**
     * Return the current app description
     *
     * @return string $description
     */
    public function current() {
    	
    	$apps = $this->getApps();
        
    	return $this->getApp($apps[$this->current]);
        
    }
	
    /**
     * Return the current app name
     *
     * @return string $name
     */
    public function key() {
    	
    	$apps = $this->getApps();
    	
    	return $apps[$this->current];
        
    }
	
    /**
     * Fetch the iterator
     *
     * @return Apps $this
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
    	
    	$apps = $this->getApps();
    	
    	return isset($apps[$this->current]);
        
    }
	
    /**
     * The following methods implement the ArrayAccess interface
     */
	
    /**
     * Check if an offset exists
     *
     * @param string $name
     *
     * @return boolean $hasApp
     */
    public function offsetExists($name) {
    	
    	return isset($this->apps[$name]);
        
    }
	
    /**
     * Get a app description
     *
     * @param string $name
     *
     * @return string $description
     */
    public function offsetGet($name) {
    	
        return $this->getApp($name);
        
    }
	
    /**
     * Set a app
     *
     * @param string $name
     * @param PackageApp $app
     *
     * @return Apps $this
     */
    public function offsetSet($name, $app) {
    	
    	$app->setName($name)->save();
    	
    	$this->apps[$name] = $app->getID();
        
        return $this;
        
    }
	
    /**
     * Remove a app
     *
     * @param string $name
     *
     * @return Apps $this
     */
    public function offsetUnset($name) {
        
        return $this->removeApp($name);
        
    }
	
    /**
     * The following methods implement the Countable interface
     */
	
    /**
     * Return the amount of apps loaded
     *
     * @return int $count
     */
    public function count() {
    	
    	$apps = $this->getApps();
    	
    	return count($apps);
        
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
            $this->apps
        );
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Apps $this
     */
    public function unserialize($data) {
    	
    	$this->apps = unserialize($data);
        
        return $this;
        
    }

}
