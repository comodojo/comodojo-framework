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

class RpcSignature {
	
	private $ret    = "";
	
	private $params = array();
	
	function __construct() {
		
		$this->dbh  = $dbh;
		
	}
	
	public function getReturnType() {
		
		return $this->ret;
		
	}
	
	public function setReturnType($returnType) {
		
		$this->ret = $returnType;
		
		return $this;
		
	}
	
	public function getRawParameters() {
		
		return $this->params;
		
	}
	
	public function setRawParameters($params) {
		
		$this->params = $params;
		
		return $this;
		
	}
	
	public function getParameters() {
		
		return array_keys($this->params);
		
	}
	
	public function getParameterType($name) {
		
		if (isset($this->params[$name]))
			return $this->params[$name]['type'];
		
		return null;
		
	}
	
	public function isParameterOptional($name) {
		
		if (isset($this->params[$name]))
			return filter_var($this->params[$name]['optional'], FILTER_VALIDATE_BOOLEAN);
		
		return null;
		
	}
	
	public function hasParameter($name) {
		
		return (isset($this->params[$name]));
		
	}
	
	public function addParameter($name, $type, $optional = false) {
		
		$this->params[$name] = array(
			"type"     => $type,
			"optional" => filter_var($optional, FILTER_VALIDATE_BOOLEAN)
		);
		
		return $this;
		
	}
	
	public function removeParameter($name) {
		
		if (isset($this->params[$name]))
			unset($this->params[$name]);
		
		return $this;
		
	}

}

