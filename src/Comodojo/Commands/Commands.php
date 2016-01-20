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

class Commands implements \Iterator, \ArrayAccess, \Countable, \Serializable {
	
	private $commands  = array();
	
	private $current  = 0;
	
	private $dbh      = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh  = $dbh;
		
		$this->loadCommands();
		
	}
	
	public function getCommand($name) {
		
		if (!isset($this->commands[$name]))
			return null;
			
		return Command::loadCommand($this->commands[$name], $this->dbh);
		
	}
	
	public function getCommands() {
		
		return array_keys($this->commands);
		
	}
	
	public function removeCommand($name) {
		
		if (isset($this->commands[$name])) {
			
			unset($this->commands[$name]);
			
			Command::loadCommand($this->commands[$name], $this->dbh)->delete();
			
		}
		
		return $this;
		
	}
	
	private function loadCommands() {
		
		$this->commands = array();
		
		$query = "SELECT * FROM comodojo_commands ORDER BY command";
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {
            
                $this->commands[$row['command']] = intval($row['id']);
            
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
     * @return Commands $this
     */
    public function rewind() {
			
		$this->current  = 0;
    	
    	return $this;
        
    }
	
    /**
     * Return the current command description
     *
     * @return string $description
     */
    public function current() {
    	
    	$commands = $this->getCommands();
        
    	return $this->getCommand($commands[$this->current]);
        
    }
	
    /**
     * Return the current command name
     *
     * @return string $name
     */
    public function key() {
    	
    	$commands = $this->getCommands();
    	
    	return $commands[$this->current];
        
    }
	
    /**
     * Fetch the iterator
     *
     * @return Commands $this
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
    	
    	$commands = $this->getCommands();
    	
    	return isset($commands[$this->current]);
        
    }
	
    /**
     * The following methods implement the ArrayAccess interface
     */
	
    /**
     * Check if an offset exists
     *
     * @param string $name
     *
     * @return boolean $hasCommand
     */
    public function offsetExists($name) {
    	
    	return isset($this->commands[$name]);
        
    }
	
    /**
     * Get a command description
     *
     * @param string $name
     *
     * @return string $description
     */
    public function offsetGet($name) {
    	
        return $this->getCommand($name);
        
    }
	
    /**
     * Set a command
     *
     * @param string $name
     * @param Command $command
     *
     * @return Commands $this
     */
    public function offsetSet($name, $command) {
    	
    	$command->setName($name)->save();
    	
    	$this->commands[$name] = $command->getID();
        
        return $this;
        
    }
	
    /**
     * Remove a command
     *
     * @param string $name
     *
     * @return Commands $this
     */
    public function offsetUnset($name) {
        
        return $this->removeCommand($name);
        
    }
	
    /**
     * The following methods implement the Countable interface
     */
	
    /**
     * Return the amount of commands loaded
     *
     * @return int $count
     */
    public function count() {
    	
    	$commands = $this->getCommands();
    	
    	return count($commands);
        
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
    		$this->commands
    	);
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Commands $this
     */
    public function unserialize($data) {
    	
    	$this->commands = unserialize($data);
        
        return $this;
        
    }

}
