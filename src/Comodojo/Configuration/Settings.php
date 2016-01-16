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
	
	public function getValue($name) {
		
		if (!isset($this->settings[$name]))
			return false;
		
		return $this->settings[$name]['value'];
		
	}
	
	public function setValue($name, $value) {
		
		if (!isset($this->settings[$name])) {
			
			$this->settings[$name] = array();
			$this->settings[$name]['id'] = 0;
			
		}
		
		$this->settings[$name]['value'] = $value;
		
		$this->saveSetting($name);
		
		return $this;
		
	}
	
	public function getSettings() {
		
		return array_keys($this->settings);
		
	}
	
	public function removeSetting($name) {
		
		if (isset($this->settings[$name])) {
			
			unset($this->settings[$name]);
			
			$this->deleteSetting($name);
			
		}
		
		return $this;
		
	}
	
	private function saveSetting($name) {
		
		if (isset($this->settings[$name])) {
		
			if ($this->settings[$name]['id'] == 0) {
				
				$this->createSetting($name);
				
			} else {
				
				$this->updateSetting($name);
				
			}
			
			ksort($this->settings);
			
		}
		
		return $this;
		
	}
	
	private function createSetting($name) {
		
		$query = sprintf("INSERT INTO comodojo_settings VALUES (0, '%s', '%s')",
			mysqli_real_escape_string($this->dbh->getHandler(), $name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->settings[$name]['value'])
		);
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        $this->settings[$name]['id'] = $result->getInsertId();
        
        return $this;
		
	}
	
	private function updateSetting($name) {
		
		$query = sprintf("UPDATE comodojo_settings SET name = '%s', value = '%s' WHERE id = %d",
			mysqli_real_escape_string($this->dbh->getHandler(), $name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->settings[$name]['value']),
			$this->settings[$name]['id']
		);
		       
        try {
            
            $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        return $this;
		
	}
	
	private function deleteSetting($name) {
		
		$query = sprintf("DELETE FROM comodojo_settings WHERE name = '%s'",
			mysqli_real_escape_string($this->dbh->getHandler(), $name)
		);
		       
        try {
            
            $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
		
		return $this;
		
	}
	
	private function loadSettings() {
		
		$this->settings = array();
		
		$query = "SELECT * FROM comodojo_settings ORDER BY name";
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {
            
                $this->settings[$row['name']] = array();
                
                $this->settings[$row['name']]['id']    = intval($row['id']);
                $this->settings[$row['name']]['value'] = $row['value'];
            
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
    	
    	$settings = $this->getSettings();
        
    	return $this->getValue($settings[$this->current]);
        
    }
	
    /**
     * Return the current setting name
     *
     * @return string $name
     */
    public function key() {
    	
    	$settings = $this->getSettings();
    	
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
    	
    	$settings = $this->getSettings();
    	
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
    	
    	return isset($this->devices[$name]);
        
    }
	
    /**
     * Get a setting value
     *
     * @param string $name
     *
     * @return string $value
     */
    public function offsetGet($name) {
    	
        return $this->getValue($name);
        
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
    	
        $this->setValue($name, $value);
        
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
        
        return $this->removeSetting($name);
        
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
