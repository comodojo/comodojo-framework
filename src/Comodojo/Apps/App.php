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

class App implements \Serializable {
	
	private $id   = 0;
	
	private $name = "";
	
	private $pack = "";
	
	private $desc = "";
	
	private $dbh  = null;
	
	function __construct($package, Database $dbh) {
		
		$this->pack = $package;
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
	
	public function getRoles() {
		
		$roles = array();
		
		
		$query = sprintf("SELECT comodojo_roles.id as id 
		FROM 
			comodojo_roles, 
			comodojo_apps_to_roles 
		WHERE 
			comodojo_roles.id = comodojo_apps_to_roles.role AND 
			comodojo_apps_to_roles.app = %d",
			$this->id
		);
		       
        try {
            
            $result = $dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        if ($result->getLength() > 0) {
        	
        	$data = $result->getData();
        	
        	foreach ($data as $row) {
			
				$manager = new RolesManager($this->dbh);
        		
        		array_push($roles, $manager->getRoleByID($row['id']));
        		
        	}
			
        }
		
		return $roles;
		
	}
	
	public static function loadApp($id, $dbh) {
		
		$query = sprintf("SELECT * FROM comodojo_apps WHERE id = %d",
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
        	
        	$app = new App($data['package'], $dbh);
        	
        	$app->id   = $data['id'];
        	$app->name = $data['name'];
        	$app->desc = $data['desc'];
        	
        	return $app;
        	
        }
		
	}
	
	public function delete() {
		
		$query = sprintf("DELETE FROM comodojo_apps WHERE id = %d",
			$this->id
		);
		       
        try {
            
            $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        $this->id   = 0;
        $this->name = "";
        $this->desc = "";
        $this->pack = "";
		
		return $this;
		
	}
	
	public function save() {
		
		if ($this->id == 0) {
			
			$this->createApp();
			
		} else {
			
			$this->updateApp($name);
			
		}
		
		return $this;
		
	}
	
	private function createApp() {
		
		$query = sprintf("INSERT INTO comodojo_apps VALUES (0, '%s', '%s', '%s')",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
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
	
	private function updateApp() {
		
		$query = sprintf("UPDATE comodojo_apps SET name = '%s', description = '%s', package = '%s' WHERE id = %d",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
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
            $this->desc,
            $this->pack
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
    	
    	$appData = unserialize($data);
    	
    	$this->id   = intval($appData[0]);
    	$this->name = $appData[1];
    	$this->desc = $appData[2];
    	$this->pack = $appData[3];
        
        return $this;
        
    }

}
