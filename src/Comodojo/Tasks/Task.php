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

class Task implements \Serializable {
	
	private $id   = 0;
	
	private $name = "";
	
	private $cls  = "";
	
	private $pack = "";
	
	private $desc = "";
	
	private $dbh  = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh  = $dbh;
		
	}
	
	public function getID() {
		
		return $this->id;
		
	}
	
	public function getName() {
		
		return $this->name;
		
	}
	
	public function setName($name) {
		
		$this->name = $name;
		
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
	
	public function getDescription() {
		
		return $this->description;
		
	}
	
	public function setDescription($description) {
		
		$this->description = $description;
		
		return $this;
		
	}
	
	public function getPackageName() {
		
		return $this->pack;
		
	}
	
	public function setPackageName($name) {
		
		$this->pack = $name;
		
		return $this;
		
	}
	
	public static function loadTask($id, $dbh) {
		
		$query = sprintf("SELECT * FROM comodojo_tasks WHERE id = %d",
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
        	
        	$task = new Task($dbh);
        	
        	$task->id   = $data['id'];
        	$task->name = $data['name'];
        	$task->cls  = $data['class'];
        	$task->desc = $data['description'];
        	$task->pack = $data['package'];
        	
        	return $task;
        	
        }
		
	}
	
	public function delete() {
		
		$query = sprintf("DELETE FROM comodojo_tasks WHERE id = %d",
			$this->id
		);
		       
        try {
            
            $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        $this->id   = 0;
        $this->name = "";
        $this->cls  = "";
        $this->desc = "";
        $this->pack = "";
		
		return $this;
		
	}
	
	public function save() {
		
		if ($this->id == 0) {
			
			$this->createTask();
			
		} else {
			
			$this->updateTask($name);
			
		}
		
		return $this;
		
	}
	
	private function createTask() {
		
		$query = sprintf("INSERT INTO comodojo_tasks VALUES (0, '%s', '%s', '%s', '%s')",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->desc),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->pack)
		);
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        $this->id = $result->getInsertId();
        
        return $this;
		
	}
	
	private function updateTask() {
		
		$query = sprintf("UPDATE comodojo_tasks SET name = '%s', class = '%s', description = '%s', package = '%s' WHERE id = %d",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->desc),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->pack),
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
            $this->name,
            $this->cls,
            $this->desc,
            $this->pack
        ));
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Tasks $this
     */
    public function unserialize($data) {
    	
    	$taskData = unserialize($data);
    	
    	$this->id   = intval($taskData[0]);
    	$this->name = $taskData[1];
    	$this->cls  = $taskData[2];
    	$this->desc = $taskData[3];
    	$this->pack = $taskData[4];
        
        return $this;
        
    }

}
