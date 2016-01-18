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

class Apps implements \Iterator, \ArrayAccess, \Countable, \Serializable {
	
	private $apps     = array();
	
	private $current  = 0;
	
	private $dbh      = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh = $dbh;
		
		$this->loadApps();
		
	}
	
	public function getPackageApps($name) {
		
		if (in_array($name, $this->apps))
			return new PackageApps($name, $this->dbh);
		
		return null;
		
	}
	
	public function setPackageApps($name, PackageApps $value) {
		
    	if ($value->getPackageName() !== $name) {
    		
    		$value->setPackageName($name);
    		
    	}
    	
    	return $this->loadApps();
		
	}
	
	public function removePackageApps($name) {
		
		if (in_array($name, $this->apps)) {
			
			$apps = new PackageApps($name, $this->dbh);
			
			foreach($apps->getApps() as $app) {
				
				$apps->removeApp($app);
				
			}
			
		}
		
    	$this->loadApps();
		
	}
	
	public function getPackages() {
		
		$this->loadApps();
		
		return $this->apps;
		
	}
	
	private function loadApps() {
		
		$this->apps = array();
		
		$query = "SELECT distinct package as p FROM comodojo_apps ORDER BY name";
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {
            
            	array_push($this->apps, $row['p']);
            
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
     * Return the current app value
     *
     * @return string $value
     */
    public function current() {
    	
    	$apps = $this->getPackages();
        
    	return $this->getPackageApps($apps[$this->current]);
        
    }
	
    /**
     * Return the current app name
     *
     * @return string $name
     */
    public function key() {
    	
    	$apps = $this->getPackages();
    	
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
     * Check if there's a next value
     *
     * @return boolean $hasNext
     */
    public function valid() {
    	
    	$apps = $this->getPackages();
    	
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
    	
    	return in_array($name, $this->apps);
        
    }
	
    /**
     * Get a app value
     *
     * @param string $name
     *
     * @return string $value
     */
    public function offsetGet($name) {
    	
        return $this->getPackageApps($name);
        
    }
	
    /**
     * Set a app
     *
     * @param string $name
     * @param string $value
     *
     * @return Apps $this
     */
    public function offsetSet($name, $value) {
    	
    	$this->setPackageApps($name, $value);
        
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
        
        return $this->removePackageApps($name);
        
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
