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

class Themes implements \Iterator, \ArrayAccess, \Countable, \Serializable {
	
	private $themes     = array();
	
	private $current  = 0;
	
	private $dbh      = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh  = $dbh;
		
		$this->loadThemes();
		
	}
	
	public function getTheme($name) {
		
		if (!isset($this->themes[$name]))
			return null;
			
		return Theme::loadTheme($this->themes[$name], $this->dbh);
		
	}
	
	public function getThemes() {
		
		return array_keys($this->themes);
		
	}
	
	public function removeTheme($name) {
		
		if (isset($this->themes[$name])) {
			
			unset($this->themes[$name]);
			
			Theme::loadTheme($this->themes[$name], $this->dbh)->delete();
			
		}
		
		return $this;
		
	}
	
	private function loadThemes() {
		
		$this->themes = array();
		
		$query = sprintf("SELECT * FROM comodojo_themes WHERE package = '%s' ORDER BY name",
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
            
                $this->themes[$row['name']] = intval($row['id']);
            
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
     * @return Themes $this
     */
    public function rewind() {
			
		$this->current  = 0;
    	
    	return $this;
        
    }
	
    /**
     * Return the current theme description
     *
     * @return string $description
     */
    public function current() {
    	
    	$themes = $this->getThemes();
        
    	return $this->getTheme($themes[$this->current]);
        
    }
	
    /**
     * Return the current theme name
     *
     * @return string $name
     */
    public function key() {
    	
    	$themes = $this->getThemes();
    	
    	return $themes[$this->current];
        
    }
	
    /**
     * Fetch the iterator
     *
     * @return Themes $this
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
    	
    	$themes = $this->getThemes();
    	
    	return isset($themes[$this->current]);
        
    }
	
    /**
     * The following methods implement the ArrayAccess interface
     */
	
    /**
     * Check if an offset exists
     *
     * @param string $name
     *
     * @return boolean $hasTheme
     */
    public function offsetExists($name) {
    	
    	return isset($this->themes[$name]);
        
    }
	
    /**
     * Get a theme description
     *
     * @param string $name
     *
     * @return string $description
     */
    public function offsetGet($name) {
    	
        return $this->getTheme($name);
        
    }
	
    /**
     * Set a theme
     *
     * @param string $name
     * @param Theme $theme
     *
     * @return Themes $this
     */
    public function offsetSet($name, $theme) {
    	
    	$theme->setName($name)->save();
    	
    	$this->themes[$name] = $theme->getID();
        
        return $this;
        
    }
	
    /**
     * Remove a theme
     *
     * @param string $name
     *
     * @return Themes $this
     */
    public function offsetUnset($name) {
        
        return $this->removeTheme($name);
        
    }
	
    /**
     * The following methods implement the Countable interface
     */
	
    /**
     * Return the amount of themes loaded
     *
     * @return int $count
     */
    public function count() {
    	
    	$themes = $this->getThemes();
    	
    	return count($themes);
        
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
            $this->themes
        );
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Themes $this
     */
    public function unserialize($data) {
    	
    	$this->themes = unserialize($data);
        
        return $this;
        
    }

}
