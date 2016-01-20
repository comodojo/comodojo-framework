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

class RpcMethod implements \Serializable {
	
	private $id         = 0;
	
	private $name       = "";
	
	private $callback   = "";
	
	private $method     = "";
	
	private $desc       = "";
	
	private $signatures = array();
	
	private $pack       = "";
	
	private $dbh        = null;
	
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
	
	public function getRawSignatures() {
		
		$signatures = array();
		
		foreach ($this->signatures as $signature) {
			
			array_push($signatures, array(
				'returnType' => $signature->getReturnType(),
				'parameters' => $signature->getRawParameters()
			));
			
		}
		
		return $signatures;
		
	}
	
	public function setRawSignatures($signatures) {
		
		foreach ($signatures as $signature) {
			
			$sig = new RpcSignature();
			
			$sig->setReturnType($signature['returnType']);
			
			$sig->setRawParameters($signature['parameters']);
			
			array_push($this->signatures, $sig);
			
		}
		
		return $this;
		
	}
	
	public function countSignatures() {
		
		return count($this->signatures);
		
	}
	
	public function getSignatures() {
		
		return $this->signatures;
		
	}
	
	public function setSignatures($signatures) {
		
		$this->signatures = $signatures;
		
		return $this;
		
	}
	
	public function addSignature($signature) {
		
		array_push($this->signatures, $signature);
		
		return $this;
		
	}
	
	public function getSignature($index) {
		
		if (isset($this->signatures[$index]))
			return $this->signatures[$index];
		
		return null;
		
	}
	
	public function removeSignature($index) {
		
		if (isset($this->signatures[$index])) {
			
			array_splice($this->signatures, $index, 1);
			
		}
		
		return null;
		
	}
	
	public function getDescription() {
		
		return $this->desc;
		
	}
	
	public function setDescription($description) {
		
		$this->desc = $description;
		
		return $this;
		
	}
	
	public function getPackageName() {
		
		return $this->pack;
		
	}
	
	public function setPackageName($name) {
		
		$this->pack = $name;
		
		return $this;
		
	}
	
	public static function loadRpcMethod($id, $dbh) {
		
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
        	
        	$data = $data[0];
        	
        	$rpc = new Rpc($dbh);
        	
        	$rpc->id         = $data['id'];
	    	$rpc->name       = $data['name'];
	        $rpc->pack       = $data['package'];
	        $rpc->callback   = $data['callback'];
	        $rpc->method     = $data['method'];
	        $rpc->desc       = $data['description'];
	        $rpc->setRawSignatures(json_decode($data['signatures'], true));
        	
        	return $rpc;
        	
        }
		
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
        
        $this->id         = 0;
    	$this->name       = "";
        $this->pack       = "";
        $this->callback   = "";
        $this->method     = "";
        $this->desc       = "";
        $this->signatures = array();
		
		return $this;
		
	}
	
	public function save() {
		
		if ($this->id == 0) {
			
			$this->createRpcMethod();
			
		} else {
			
			$this->updateRpcMethod($name);
			
		}
		
		return $this;
		
	}
	
	private function createRpcMethod() {
		
		$query = sprintf("INSERT INTO comodojo_rpc VALUES (0, '%s', '%s', '%s', '%s', '%s', '%s')",
			mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->callback),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->method),
			mysqli_real_escape_string($this->dbh->getHandler(), $this->desc),
			mysqli_real_escape_string($this->dbh->getHandler(), json_encode($this->getRawSignatures())),
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
	
	private function updateRpcMethod() {
		
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
	        $this->callback,
	        $this->method,
	        $this->desc,
	        json_encode($this->getRawSignatures())
        ));
        
    }
	
    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Rpc $this
     */
    public function unserialize($data) {
    	
    	$commandData = unserialize($data);
    	
    	$this->id         = intval($rpcData[0]);
    	$this->name       = $rpcData[1];
        $this->pack       = $rpcData[2];
        $this->callback   = $rpcData[3];
        $this->method     = $rpcData[4];
        $this->desc       = $rpcData[5];
        $this->setRawSignatures(json_decode($rpcData[6], true));
        
        return $this;
        
    }

}

