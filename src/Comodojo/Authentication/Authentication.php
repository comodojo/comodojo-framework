<?php namespace Comodojo\Authentication;

use \Comodojo\Database\Database;
use \Comodojo\Exception\DatabaseException;
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

class Authentication implements \Serializable {
	
	private $id      = 0;
	
	private $name    = "";
	
	private $desc    = "";
	
	private $cls     = "";
	
	private $param   = array();
	
	private $package = "";
	
	private $dbh     = null;
	
	function __construct(Database $dbh) {
		
		$this->dbh = $dbh;
		
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
	
	public function getDescription() {
		
		return $this->desc;
		
	}
	
	public function setDescription($desc) {
		
		$this->desc = $desc;
		
		return $this;
		
	}
	
	public function getPackage() {
		
		return $this->package;
		
	}
	
	public function setPackage($package) {
		
		$this->package = $package;
		
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
	
	public function getParameters() {
		
		return array_keys($this->params);
		
	}
	
	public function getParameter($name) {
		
		return $this->params[$name];
		
	}
	
	public function setParameter($name, $value) {
		
		$this->params[$name] = $value;
		
		return $this;
		
	}
	
	public function unsetParameter($name) {
		
		if (isset($this->params[$name]))
			unset($this->params[$name]);
		
		return $this;
		
	}
	
	public function save() {
		
		if ($this->id == 0) {
			
			$this->create();
			
		} else {
			
			$this->update();
			
		}
        
        return $this;
		
	}
	
	private function create() {
		
		$query = sprintf("INSERT INTO comodojo_authentication VALUES (0, '%s', '%s', '%s', '%s', '%s')",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->description),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
			mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->param)),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->package)
		);
		       
        try {
            
            $result = $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        $this->id = $result->getInsertId();
        
        return $this;
		
	}
	
	private function update() {
		
		$query = sprintf("UPDATE comodojo_authentication SET name = '%s', value = '%s', class = '%s', parameters = '%s', package = '%s' WHERE id = %d",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->description),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
			mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->param)),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->package),
			$this->id
		);
		       
        try {
            
            $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        return $this;
		
	}
	
	public function delete() {
		
		$query = sprintf("DELETE FROM comodojo_authentication WHERE id = %d",
			$this->id
		);
		       
        try {
            
            $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        $this->id      = 0;
        $this->name    = "";
        $this->desc    = "";
        $this->cls     = "";
        $this->param   = array();
        $this->package = "";
		
		return $this;
		
	}
	
	public static function loadData($dbh, $data) {
		
		$auth = new Authentication($dbh);
		
		$auth->id      = $data[0];
		$auth->name    = $data[1];
		$auth->desc    = $data[2];
		$auth->cls     = $data[3];
		$auth->param   = json_decode($data[4]);
		$auth->package = $data[5];
        
        return $auth;
		
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
    	
    	$data = array(
    		$this->id,
    		$this->name,
    		$this->desc,
    		$this->cls,
    		json_encode($this->param),
    		$this->package
    	);
    	
    	return serialize(
            $data
        );
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Manager $this
     */
    public function unserialize($data) {
    	
    	$data = unserialize($data);
    	
		$auth->id      = $data[0];
		$auth->name    = $data[1];
		$auth->desc    = $data[2];
		$auth->cls     = $data[3];
		$auth->param   = json_decode($data[4]);
		$auth->package = $data[5];
        
        return $this;
        
    }
	

}
