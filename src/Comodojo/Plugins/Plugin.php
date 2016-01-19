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

class Plugin implements \Serializable {
	
	private $id     = 0;
	
	private $name   = "";
	
	private $pack   = "";
	
	private $cls    = "";
	
	private $method = "";
	
	private $event  = "";
	
	private $fw     = "";
	
	private $dbh    = null;
	
	function __construct($framework, Database $dbh) {
		
		$this->fw   = $framework;
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
	
	public function getMethod() {
		
		return $this->method;
		
	}
	
	public function setMethod($method) {
		
		$this->method = $method;
		
		return $this;
		
	}
	
	public function execute() {
		
		$obj = $this->getInstance();
		
		$params = func_get_args();
		
		if (!is_null($obj)) {
			
			if (!empty($this->method) && method_exists($obj, $this->method)) {
				
				return call_user_func(array($obj, $this->method), $params);
				
			}
			
		}
		
		return null;
		
	}
	
	public function getEventName() {
		
		return $this->event;
		
	}
	
	public function setEventName($name) {
		
		$this->event = $name;
		
		return $this;
		
	}
	
	public function getFramework() {
		
		return $this->fw;
		
	}
	
	public function setFramework($name) {
		
		$this->fw = $name;
		
		return $this;
		
	}
	
	public function getPackageName() {
		
		return $this->pack;
		
	}
	
	public function setPackageName($name) {
		
		$this->pack = $name;
		
		return $this;
		
	}
	
	public static function loadPlugin($id, $dbh) {
		
		$query = sprintf("SELECT * FROM comodojo_plugins WHERE id = %d",
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
        	
        	$plugin = new Plugin($data['framework'], $dbh);
        	
        	$plugin->id     = $data['id'];
        	$plugin->name   = $data['name'];
        	$plugin->pack   = $data['package'];
        	$plugin->cls    = $data['class'];
        	$plugin->method = $data['method'];
        	$plugin->event  = $data['event'];
        	
        	return $plugin;
        	
        }
		
	}
	
	public function delete() {
		
		$query = sprintf("DELETE FROM comodojo_plugins WHERE id = %d",
			$this->id
		);
		       
        try {
            
            $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        $this->id     = 0;
        $this->name   = "";
        $this->pack   = "";
        $this->fw     = "";
        $this->cls    = "";
        $this->method = "";
        $this->event  = "";
		
		return $this;
		
	}
	
	public function save() {
		
		if ($this->id == 0) {
			
			$this->createPlugin();
			
		} else {
			
			$this->updatePlugin($name);
			
		}
		
		return $this;
		
	}
	
	private function createPlugin() {
		
		$query = sprintf("INSERT INTO comodojo_plugins VALUES (0, '%s', '%s', '%s', '%s', '%s', '%s')",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->method),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->event),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->fw),
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
	
	private function updatePlugin() {
		
		$query = sprintf("UPDATE comodojo_plugins SET name = '%s', class = '%s', method = '%s', event = '%s', framework = '%s', package = '%s' WHERE id = %d",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->method),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->event),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->fw),
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
	        $this->pack,
	        $this->fw,
	        $this->cls,
	        $this->method,
	        $this->event 
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
    	
    	$pluginData = unserialize($data);
    	
    	$this->id     = intval($pluginData[0]);
    	$this->name   = $pluginData[1];
        $this->pack   = $pluginData[2];
        $this->fw     = $pluginData[3];
        $this->cls    = $pluginData[4];
        $this->method = $pluginData[5];
        $this->event  = $pluginData[6];
        
        return $this;
        
    }

}
