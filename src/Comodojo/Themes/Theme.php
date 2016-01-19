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

class Theme implements \Serializable {
	
	private $id   = 0;
	
	private $name = "";
	
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
	
	public static function loadTheme($id, $dbh) {
		
		$query = sprintf("SELECT * FROM comodojo_themes WHERE id = %d",
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
        	
        	$theme = new Theme($dbh);
        	
        	$theme->id   = $data['id'];
        	$theme->name = $data['name'];
        	$theme->desc = $data['desc'];
        	$theme->pack = $data['package'];
        	
        	return $theme;
        	
        }
		
	}
	
	public function delete() {
		
		$query = sprintf("DELETE FROM comodojo_themes WHERE id = %d",
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
			
			$this->createTheme();
			
		} else {
			
			$this->updateTheme($name);
			
		}
		
		return $this;
		
	}
	
	private function createTheme() {
		
		$query = sprintf("INSERT INTO comodojo_themes VALUES (0, '%s', '%s', '%s')",
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
	
	private function updateTheme() {
		
		$query = sprintf("UPDATE comodojo_themes SET name = '%s', description = '%s', package = '%s' WHERE id = %d",
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
     * @return Themes $this
     */
    public function unserialize($data) {
    	
    	$themeData = unserialize($data);
    	
    	$this->id   = intval($themeData[0]);
    	$this->name = $themeData[1];
    	$this->desc = $themeData[2];
    	$this->pack = $themeData[3];
        
        return $this;
        
    }

}
