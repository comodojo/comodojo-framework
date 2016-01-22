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

abstract class ConfigElement implements \Serializable {
	
	protected $id      = 0;
	
	protected $name    = "";
	
	protected $package = "";
	
	protected $dbh     = null;
	
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
	
	public function getPackageName() {
		
		return $this->package;
		
	}
	
	public function setPackageName($name) {
		
		$this->package = $name;
		
		return $this;
		
	}
	
	abstract public static function load($id, $dbh);
	
	public function save() {
		
		if ($this->id == 0) {
			
			$this->create();
			
		} else {
			
			$this->update();
			
		}
		
		return $this;
		
	}
	
	abstract protected function getData();
	
	abstract protected function setData($data);
	
	abstract protected function create();
	
	abstract protected function update();
	
	abstract public function delete();
	
    /**
     * The following methods implement the Serializable interface
     */
	
    /**
     * Return the serialized data
     *
     * @return string $serialized
     */
    public function serialize() {
    	
    	$data = $this->getData();
    	
    	return serialize($data);
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Routes $this
     */
    public function unserialize($data) {
    	
    	$data = unserialize($data);
    	
    	$this->setData($data);
        
        return $this;
        
    }

}
