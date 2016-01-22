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

class App extends ConfigElement {
	
	protected $desc = "";
	
	public function getDescription() {
		
		return $this->description;
		
	}
	
	public function setDescription($description) {
		
		$this->description = $description;
		
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
			
        		array_push($roles, Role::load(intval($row['id']), $this->dbh));
        		
        	}
			
        }
		
		return $roles;
		
	}
	
	public static function load($id, $dbh) {
		
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
        	
        	$data = array_values($data[0]);
        	
        	$app = new App($dbh);
        	
        	$app->setData($data);
        	
        	return $app;
        	
        }
		
	}
	
    protected function getData() {
    	
    	return array(
            $this->id,
            $this->name,
            $this->desc,
            $this->package
        );
        
    }
	
	protected function setData($data) {
    	
    	$this->id      = intval($data[0]);
    	$this->name    = $data[1];
    	$this->desc    = $data[2];
    	$this->package = $data[3];
        
        return $this;
        
    }
	
	protected function create() {
		
		$query = sprintf("INSERT INTO comodojo_apps VALUES (0, '%s', '%s', '%s')",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->desc),
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
	
	protected function update() {
		
		$query = sprintf("UPDATE comodojo_apps SET name = '%s', description = '%s', package = '%s' WHERE id = %d",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->desc),
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
		
		$query = sprintf("DELETE FROM comodojo_apps WHERE id = %d",
			$this->id
		);
		       
        try {
            
            $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        	
        $this->setData(array(0, "", "", ""));
		
		return $this;
		
	}

}
