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

class Tasks implements \Iterator, \ArrayAccess, \Countable, \Serializable {
	
	private $tasks    = array();
	
	private $current  = 0;
	
	private $dbh      = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh  = $dbh;
		
		$this->loadTasks();
		
	}
	
	public function getTask($name) {
		
		if (!isset($this->tasks[$name]))
			return null;
			
		return Task::loadTask($this->tasks[$name], $this->dbh);
		
	}
	
	public function getTasks() {
		
		return array_keys($this->tasks);
		
	}
	
	public function removeTask($name) {
		
		if (isset($this->tasks[$name])) {
			
			unset($this->tasks[$name]);
			
			Task::loadTask($this->tasks[$name], $this->dbh)->delete();
			
		}
		
		return $this;
		
	}
	
	private function loadTasks() {
		
		$this->tasks = array();
		
		$query = "SELECT * FROM comodojo_tasks ORDER BY name";
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {

            $data = $result->getData();

            foreach ($data as $row) {
            
                $this->tasks[$row['name']] = intval($row['id']);
            
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
     * @return Tasks $this
     */
    public function rewind() {
			
		$this->current  = 0;
    	
    	return $this;
        
    }
	
    /**
     * Return the current task description
     *
     * @return string $description
     */
    public function current() {
    	
    	$tasks = $this->getTasks();
        
    	return $this->getTask($tasks[$this->current]);
        
    }
	
    /**
     * Return the current task name
     *
     * @return string $name
     */
    public function key() {
    	
    	$tasks = $this->getTasks();
    	
    	return $tasks[$this->current];
        
    }
	
    /**
     * Fetch the iterator
     *
     * @return Tasks $this
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
    	
    	$tasks = $this->getTasks();
    	
    	return isset($tasks[$this->current]);
        
    }
	
    /**
     * The following methods implement the ArrayAccess interface
     */
	
    /**
     * Check if an offset exists
     *
     * @param string $name
     *
     * @return boolean $hasTask
     */
    public function offsetExists($name) {
    	
    	return isset($this->tasks[$name]);
        
    }
	
    /**
     * Get a task description
     *
     * @param string $name
     *
     * @return string $description
     */
    public function offsetGet($name) {
    	
        return $this->getTask($name);
        
    }
	
    /**
     * Set a task
     *
     * @param string $name
     * @param Task $task
     *
     * @return Tasks $this
     */
    public function offsetSet($name, $task) {
    	
    	$task->setName($name)->save();
    	
    	$this->tasks[$name] = $task->getID();
        
        return $this;
        
    }
	
    /**
     * Remove a task
     *
     * @param string $name
     *
     * @return Tasks $this
     */
    public function offsetUnset($name) {
        
        return $this->removeTask($name);
        
    }
	
    /**
     * The following methods implement the Countable interface
     */
	
    /**
     * Return the amount of tasks loaded
     *
     * @return int $count
     */
    public function count() {
    	
    	$tasks = $this->getTasks();
    	
    	return count($tasks);
        
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
            $this->tasks
        );
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Tasks $this
     */
    public function unserialize($data) {
    	
    	$this->tasks = unserialize($data);
        
        return $this;
        
    }

}
