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
	
	private $packages = array();
	
	private $current  = 0;
	
	private $dbh      = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh = $dbh;
		
		$this->loadApps();
		
	}
	
	public function getPackageApps($name) {
		
		return $this->packages[$name];
		
	}
	
	public function getApp($name) {
		
		if (!isset($this->apps[$name]))
			return null;
			
		return App::loadApp($this->apps[$name], $this->dbh);
		
	}
	
	public function addApp($name, $app) {
    	
    	$app->setName($name)->save();
    	
    	$this->apps[$name] = $app->getID();
    	
    	array_push($this->packages[$plugin->getPackage()], $name);
    	
    	sort($this->packages[$plugin->getPackage()]);
        
        return $this;
		
	}
	
	public function removeApp($name) {
		
		$app = $this->getApp($name);
		
		unset($this->apps[$name]);
		
		$idx = array_search($name, $this->packages[$app->getPackage()]);
		
		array_splice($this->packages[$app->getPackage()], $idx, 1);
    	
    	$app->delete();
        
        return $this;
		
	}
	
	public function getApps() {
		
		return array_keys($this->apps);
		
	}
	
	public function getPackages() {
		
		return array_keys($this->packages);
		
	}
	
	private function loadApps() {
		
		$this->apps = array();
		
		$query = "SELECT distinct package as p, name, id FROM comodojo_apps ORDER BY name";
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {
            
            	if (isset($this->packages[$row['p']]))
            		$this->packages[$row['p']] = array();
            		
            	array_push($this->packages[$row['p']], $row['name']);
            	
            	$this->apps[$row['name']] = $row['id'];
            
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
     * Check if there's a next value
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
    	
        return $this->getApp($name);
        
    }
	
    /**
     * Set a app
     *
     * @param string $name
     * @param string $value
     *
     * @return Apps $this
     */
    public function offsetSet($name, $app) {
    	
    	return $this->addApp($name, $app);
        
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
    	
    	return serialize(array(
            json_encode($this->apps),
            json_encode($this->packages)
        ));
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Apps $this
     */
    public function unserialize($data) {
    	
    	$data = unserialize($data);
    	
    	$this->apps     = json_decode($data[0], true);
    	$this->packages = json_decode($data[1], true);
        
        return $this;
        
    }

}
