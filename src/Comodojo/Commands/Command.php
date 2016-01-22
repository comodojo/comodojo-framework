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

class Command extends ConfigElement {
	
	protected $cls    = "";
	
	protected $desc   = "";
	
	protected $alias  = array();
	
	protected $opt    = array();
	
	protected $args   = array();
	
	public function getAliases() {
		
		return $this->alias;
		
	}
	
	public function setAliases($aliases) {
		
		$this->alias = $aliases;
		
		return $this;
		
	}
	
	public function addAlias($alias) {
		
		if (!in_array($alias, $this->alias)) {
			
			array_push($this->alias, $alias);
			
			sort($this->alias);
			
		}
		
		return $this;
		
	}
	
	public function removeAlias($alias) {
		
		if (in_array($alias, $this->alias)) {
			
			$idx = array_search($alias, $this->alias);
			
			array_splice($this->alias, $idx, 1);
			
		}
		
		return $this;
		
	}
	
	public function getOptions() {
		
		return array_keys($this->opt);
		
	}
	
	public function getRawOptions() {
		
		return $this->opt;
		
	}
	
	public function setRawOptions($options) {
		
		$this->opt = array();
		
		foreach ($options as $name => $opt)
			$this->addOption($name, $opt['short_name'], $opt['long_name'], $opt['action'], $opt['description']);
			
		return $this;
		
	}
	
	public function addOption($optName, $short = "", $long = "", $action = "StoreTrue", $description = "") {
		
		if (empty($short)) $short = "-"  . substr($optName, 0, 1);
		if (empty($long))  $long  = "--" . $optName;
		
		if (!isset($this->opt[$optName])) {
			
			$this->opt[$optName] = array(
				'short_name'  => $short,
				'long_name'   => $long,
				'action'      => $action,
				'description' => $description
			);
			
		}
		
		return $this;
		
	}
	
	public function removeOption($optName) {
		
		if (isset($this->opt[$optName])) {
			
			unset($this->opt[$optName]);
			
		}
		
		return $this;
		
	}
	
	public function hasOption($optName) {
		
		return (isset($this->opt[$optName]));
		
	}
	
	public function getOptionShortName($optName) {
		
		if (isset($this->opt[$optName])) {
			
			return $this->opt[$optName]['short_name'];
			
		}
		
		return null;
		
	}
	
	public function setOptionShortName($optName, $value) {
		
		if (isset($this->opt[$optName])) {
			
			$this->opt[$optName]['short_name'] = $value;
			
		}
		
		return $this;
		
	}
	
	public function getOptionLongName($optName) {
		
		if (isset($this->opt[$optName])) {
			
			return $this->opt[$optName]['long_name'];
			
		}
		
		return null;
		
	}
	
	public function setOptionLongName($optName, $value) {
		
		if (isset($this->opt[$optName])) {
			
			$this->opt[$optName]['long_name'] = $value;
			
		}
		
		return $this;
		
	}
	
	public function getOptionAction($optName) {
		
		if (isset($this->opt[$optName])) {
			
			return $this->opt[$optName]['action'];
			
		}
		
		return null;
		
	}
	
	public function setOptionAction($optName, $value) {
		
		if (isset($this->opt[$optName])) {
			
			$this->opt[$optName]['action'] = $value;
			
		}
		
		return $this;
		
	}
	
	public function getOptionDescription($optName) {
		
		if (isset($this->opt[$optName])) {
			
			return $this->opt[$optName]['description'];
			
		}
		
		return null;
		
	}
	
	public function setOptionDescription($optName, $value) {
		
		if (isset($this->opt[$optName])) {
			
			$this->opt[$optName]['description'] = $value;
			
		}
		
		return $this;
		
	}
	
	public function getArguments() {
		
		return array_keys($this->args);
		
	}
	
	public function getRawArguments() {
		
		return $this->args;
		
	}
	
	public function setRawArguments($arguments) {
		
		$this->args = array();
		
		foreach ($arguments as $name => $arg)
			$this->addArgument($name, $arg['choices'], $arg['multiple'], $arg['optional'], $arg['description']);
			
		return $this;
		
	}
	
	public function addArgument($argName, $choices = array(), $multiple = false, $optional = false, $description = "") {
		
		if (!isset($this->args[$argName])) {
			
			$this->args[$argName] = array(
				'choices'     => $choices,
				'multiple'    => $multiple,
				'optional'    => $optional,
				'description' => $description,
				'help_name'   => implode(", ", $choices)
			);
			
		}
		
		return $this;
		
	}
	
	public function removeArgument($argName) {
		
		if (isset($this->args[$argName])) {
			
			unset($this->args[$argName]);
			
		}
		
		return $this;
		
	}
	
	public function hasArgument($argName) {
		
		return (isset($this->args[$argName]));
		
	}
	
	public function getArgumentChoices($argName) {
		
		if (isset($this->args[$argName])) {
			
			return $this->args[$argName]['choices'];
			
		}
		
		return null;
		
	}
	
	public function setArgumentChoices($argName, $value) {
		
		if (isset($this->args[$argName])) {
			
			$this->args[$argName]['choices']   = $value;
			
			$this->args[$argName]['help_name'] = implode(", ", $value);
			
		}
		
		return $this;
		
	}
	
	public function getArgumentMultipleValues($argName) {
		
		if (isset($this->args[$argName])) {
			
			return $this->args[$argName]['multiple'];
			
		}
		
		return null;
		
	}
	
	public function setArgumentMultipleValues($argName, $value) {
		
		if (isset($this->args[$argName])) {
			
			$this->args[$argName]['multiple'] = $value;
			
		}
		
		return $this;
		
	}
	
	public function getArgumentOptional($argName) {
		
		if (isset($this->args[$argName])) {
			
			return $this->args[$argName]['optional'];
			
		}
		
		return null;
		
	}
	
	public function setArgumentOptional($argName, $value) {
		
		if (isset($this->args[$argName])) {
			
			$this->args[$argName]['optional'] = $value;
			
		}
		
		return $this;
		
	}
	
	public function getArgumentHelpName($argName) {
		
		if (isset($this->args[$argName])) {
			
			return $this->args[$argName]['help_name'];
			
		}
		
		return null;
		
	}
	
	public function getArgumentDescription($argName) {
		
		if (isset($this->args[$argName])) {
			
			return $this->args[$argName]['description'];
			
		}
		
		return null;
		
	}
	
	public function setArgumentDescription($argName, $value) {
		
		if (isset($this->args[$argName])) {
			
			$this->args[$argName]['description'] = $value;
			
		}
		
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
		
		return $this->desc;
		
	}
	
	public function setDescription($description) {
		
		$this->desc = $description;
		
		return $this;
		
	}
	
	public static function load($id, $dbh) {
		
		$query = sprintf("SELECT * FROM comodojo_commands WHERE id = %d",
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
        	
        	$command = new Command($dbh);
        	
        	$command->setData($data);
        	
        	return $command;
        	
        }
		
	}
	
    protected function getData() {
    	
    	return array(
            $this->id,
	        $this->name,
	        $this->cls,
	        $this->desc,
	        json_encode($this->alias),
	        json_encode($this->opt),
	        json_encode($this->args),
	        $this->package
        );
        
    }
	
	protected function setData($data) {
    	
    	$this->id     = intval($data[0]);
    	$this->name   = $data[1];
        $this->cls    = $data[2];
        $this->desc   = $data[3];
        $this->alias  = json_decode($data[4], true);
        $this->opt    = json_decode($data[5], true);
        $this->args   = json_decode($data[6], true);
        $this->package= $data[7];
        
        return $this;
        
    }
	
	protected function create() {
		
		$query = sprintf("INSERT INTO comodojo_commands VALUES (0, '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->desc),
			mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->alias)),
			mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->opt)),
			mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->args)),
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
		
		$query = sprintf("UPDATE comodojo_commands SET command = '%s', class = '%s', description = '%s', aliases = '%s', options = '%s', arguments = '%s', package = '%s' WHERE id = %d",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->cls),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->desc),
			mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->alias)),
			mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->opt)),
			mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->args)),
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
		
		$query = sprintf("DELETE FROM comodojo_commands WHERE id = %d",
			$this->id
		);
		       
        try {
            
            $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
		
        $this->setData(array(0, "", "", "", "[]", "[]", "[]", ""));
        
		return $this;
		
	}

}
