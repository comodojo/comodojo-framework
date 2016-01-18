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

class Settings implements \Iterator, \ArrayAccess, \Countable, \Serializable {
	
	private $settings = array();
	
	private $current  = 0;
	
	private $dbh      = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh = $dbh;
		
		$this->loadSettings();
		
	}
	
	public function getPackageSettings($name) {
		
		if (in_array($name, $this->settings))
			return new PackageSettings($name, $this->dbh);
		
		return null;
		
	}
	
	public function setPackageSettings($name, PackageSettings $value) {
		
    	if ($value->getPackageName() !== $name) {
    		
    		$value->setPackageName($name);
    		
    	}
    	
    	return $this->loadSettings();
		
	}
	
	public function removePackageSettings($name) {
		
		if (in_array($name, $this->settings)) {
			
			$settings = new PackageSettings($name, $this->dbh);
			
			foreach($settings->getSettings() as $setting) {
				
				$settings->removeSetting($setting);
				
			}
			
		}
		
    	$this->loadSettings();
		
	}
	
	public function getPackages() {
		
		$this->loadSettings();
		
		return $this->settings;
		
	}
	
	private function loadSettings() {
		
		$this->settings = array();
		
		$query = "SELECT distinct package as p FROM comodojo_settings ORDER BY name";
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {
            
            	array_push($this->settings, $row['p']);
            
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
     * Return the current setting value
     *
     * @return string $value
     */
    public function current() {
    	
    	$settings = $this->getPackages();
        
    	return $this->getPackageSettings($settings[$this->current]);
        
    }
	
    /**
     * Return the current setting name
     *
     * @return string $name
     */
    public function key() {
    	
    	$settings = $this->getPackages();
    	
    	return $settings[$this->current];
        
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
    	
    	$settings = $this->getPackages();
    	
    	return isset($settings[$this->current]);
        
    }
	
    /**
     * The following methods implement the ArrayAccess interface
     */
	
    /**
     * Check if an offset exists
     *
     * @param string $name
     *
     * @return boolean $hasSetting
     */
    public function offsetExists($name) {
    	
    	return in_array($name, $this->settings);
        
    }
	
    /**
     * Get a setting value
     *
     * @param string $name
     *
     * @return string $value
     */
    public function offsetGet($name) {
    	
        return $this->getPackageSettings($name);
        
    }
	
    /**
     * Set a setting
     *
     * @param string $name
     * @param string $value
     *
     * @return Settings $this
     */
    public function offsetSet($name, $value) {
    	
    	$this->setPackageSettings($name, $value);
        
        return $this;
        
    }
	
    /**
     * Remove a setting
     *
     * @param string $name
     *
     * @return Settings $this
     */
    public function offsetUnset($name) {
        
        return $this->removePackageSettings($name);
        
    }
	
    /**
     * The following methods implement the Countable interface
     */
	
    /**
     * Return the amount of settings loaded
     *
     * @return int $count
     */
    public function count() {
    	
    	$settings = $this->getSettings();
    	
    	return count($settings);
        
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
            $this->settings
        );
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Settings $this
     */
    public function unserialize($data) {
    	
    	$this->settings = unserialize($data);
        
        return $this;
        
    }

}
