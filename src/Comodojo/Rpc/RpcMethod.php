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

class RpcMethod extends ConfigElement {
	
	protected $callback   = "";
	
	protected $method     = "";
	
	protected $desc       = "";
	
	protected $signatures = array();
	
	public function getCallback() {
		
		return $this->callback;
		
	}
	
	public function setCallback($callback) {
		
		$this->callback = $callback;
		
		return $this;
		
	}
	
	public function getMethod() {
		
		return $this->method;
		
	}
	
	public function setMethod($method) {
		
		$this->method = $method;
		
		return $this;
		
	}
	
	public function getDescription() {
		
		return $this->desc;
		
	}
	
	public function setDescription($description) {
		
		$this->desc = $description;
		
		return $this;
		
	}
	
	public function getSignature($index) {
			
		if (isset($this->signatures[$index])) {
			
			return $this->signatures[$index];
			
		}
		
		return null;
		
	}
	
	public function getSignatures() {
		
		return $this->signatures;
		
	}
	
	public function setSignatures($signatures) {
		
		$this->signatures = $signatures;
		
		return $this;
		
	}
	
	public function getRawSignatures() {
		
		$signatures = array();
		
		foreach ($this->signatures as $sig) {
			
			array_push(
				$signatures,
				array(
					'returnType' => $sig->getReturnType(),
					'parameters' => $sig->getRawParameters()
				)
			);
			
		}
		
		return $signatures;
		
	}
	
	public function setRawSignatures($signatures) {
		
		foreach ($signatures as $sig) {
			
			$signature = new RpcSignature();
			
			$signature->setReturnType($sig['returnType']);
			$signature->setRawParameters($sig['parameters']);
			
			array_push(
				$this->signatures,
				$signature
			);
			
		}
		
		return $this;
		
	}
	
	public function addSignature($signature) {
			
		array_push(
			$this->signatures,
			$signature
		);
		
		return $this;
		
	}
	
	public function addRawSignature($rawSignature) {
		
		$signature = new RpcSignature();
		
		$signature->setReturnType($rawSignature['returnType']);
		$signature->setRawParameters($rawSignature['parameters']);
			
		array_push(
			$this->signatures,
			$signature
		);
		
		return $this;
		
	}
	
	public function removeSignature($index) {
			
		if (isset($this->signatures[$index])) {
			
			array_splice($this->signatures, $index, 1);
			
		}
		
		return $this;
		
	}
	
	public static function load($id, $dbh) {
		
		$query = sprintf("SELECT * FROM comodojo_rpc WHERE id = %d",
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
        	
        	$rpc = new Rpc($dbh);
        	
        	$rpc->setData($data);
        	
        	return $rpc;
        	
        }
		
	}
	
    protected function getData() {
    	
    	return array(
            $this->id,
	    	$this->name,
	        $this->callback,
	        $this->method,
	        $this->desc,
	        json_encode($this->getRawSignatures()),
	        $this->package
        );
        
    }
    
    protected function setData($data) {
    	
    	$this->id         = intval($data[0]);
    	$this->name       = $data[1];
        $this->callback   = $data[2];
        $this->method     = $data[3];
        $this->desc       = $data[4];
        $this->setRawSignatures(json_decode($data[5], true));
        $this->package    = $data[6];
        
        return $this;
        
    }
	
	protected function create() {
		
		$query = sprintf("INSERT INTO comodojo_rpc VALUES (0, '%s', '%s', '%s', '%s', '%s', '%s')",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->callback),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->method),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->desc),
			mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->getRawSignatures())),
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
		
		$query = sprintf("UPDATE comodojo_rpc SET name = '%s', callback = '%s', method = '%s', description = '%s', signatures = '%s', package = '%s' WHERE id = %d",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->callback),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->method),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->desc),
			mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->getRawSignatures())),
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
	
	public function delete() {
		
		$query = sprintf("DELETE FROM comodojo_rpc WHERE id = %d",
			$this->id
		);
		       
        try {
            
            $this->dbh->query($query);
         

        } catch (DatabaseException $de) {
            
            throw $de;

        }
        
        $this->setData(array(0, "", "", "", "", "[]", ""));
		
		return $this;
		
	}

}

