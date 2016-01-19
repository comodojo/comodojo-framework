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

class Plugins implements \Iterator, \ArrayAccess, \Countable, \Serializable {
	
	private $plugins  = array();
	
	private $fw       = array();
	
	private $current  = 0;
	
	private $dbh      = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh  = $dbh;
		
		$this->loadPlugins();
		
	}
	
	public function getPlugin($name) {
		
		if (!isset($this->plugins[$name]))
			return null;
			
		return Plugin::loadPlugin($this->plugins[$name], $this->dbh);
		
	}
	
	public function getPlugins() {
		
		return array_keys($this->plugins);
		
	}
	
	public function getSupportedFrameworks() {
		
		return array_keys($this->fw);
		
	}
	
	public function getPluginsByFramework($fw) {
		
		return $this->fw[$fw];
		
	}
	
	public function removePlugin($name) {
		
		if (isset($this->plugins[$name])) {
			
			unset($this->plugins[$name]);
			
			Plugin::loadPlugin($this->plugins[$name], $this->dbh)->delete();
			
		}
		
		return $this;
		
	}
	
	private function loadPlugins() {
		
		$this->plugins = array();
		
		$query = sprintf("SELECT * FROM comodojo_plugins WHERE package = '%s' ORDER BY name",
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
            
                $this->plugins[$row['name']] = intval($row['id']);
                
                if (!isset($this->fw[$row['framework']]))
                	$this->fw[$row['framework']] = array();
                	
                array_push($this->fw[$row['framework']], $row['name']);
            
            }
        
        }
        
        foreach ($this->fw as $fw => $list) {
        	
        	$this->fw[$fw] = sort($list);
        	
        }
        
        return $this;
		
	}
	
    /**
     * The following methods implement the Iterator interface
     */
	
    /**
     * Reset the iterator
     *
     * @return Plugins $this
     */
    public function rewind() {
			
		$this->current  = 0;
    	
    	return $this;
        
    }
	
    /**
     * Return the current plugin description
     *
     * @return string $description
     */
    public function current() {
    	
    	$plugins = $this->getPlugins();
        
    	return $this->getPlugin($plugins[$this->current]);
        
    }
	
    /**
     * Return the current plugin name
     *
     * @return string $name
     */
    public function key() {
    	
    	$plugins = $this->getPlugins();
    	
    	return $plugins[$this->current];
        
    }
	
    /**
     * Fetch the iterator
     *
     * @return Plugins $this
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
    	
    	$plugins = $this->getPlugins();
    	
    	return isset($plugins[$this->current]);
        
    }
	
    /**
     * The following methods implement the ArrayAccess interface
     */
	
    /**
     * Check if an offset exists
     *
     * @param string $name
     *
     * @return boolean $hasPlugin
     */
    public function offsetExists($name) {
    	
    	return isset($this->plugins[$name]);
        
    }
	
    /**
     * Get a plugin description
     *
     * @param string $name
     *
     * @return string $description
     */
    public function offsetGet($name) {
    	
        return $this->getPlugin($name);
        
    }
	
    /**
     * Set a plugin
     *
     * @param string $name
     * @param Plugin $plugin
     *
     * @return Plugins $this
     */
    public function offsetSet($name, $plugin) {
    	
    	$plugin->setName($name)->save();
    	
    	$this->plugins[$name] = $plugin->getID();
    	
    	array_push($this->fw[$plugin->getFramework()], $name);
    	
    	sort($this->fw[$plugin->getFramework()]);
        
        return $this;
        
    }
	
    /**
     * Remove a plugin
     *
     * @param string $name
     *
     * @return Plugins $this
     */
    public function offsetUnset($name) {
        
        return $this->removePlugin($name);
        
    }
	
    /**
     * The following methods implement the Countable interface
     */
	
    /**
     * Return the amount of plugins loaded
     *
     * @return int $count
     */
    public function count() {
    	
    	$plugins = $this->getPlugins();
    	
    	return count($plugins);
        
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
            json_encode($this->plugins),
            json_encode($this->fw)
        ));
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Plugins $this
     */
    public function unserialize($data) {
    	
    	$data = unserialize($data);
    	
    	$this->plugins = json_decode($data[0], true);
    	$this->fw      = json_decode($data[1], true);
        
        return $this;
        
    }

}
