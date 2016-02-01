<?php namespace Comodojo\Rpc;

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

    protected $return_type = "";

    protected $parameters = array();

    public function getReturnType() {

        return $this->return_type;

    }

    public function setReturnType($return_type) {

        $this->return_type = $return_type;

        return $this;

    }

    public function getRawParameters() {

        return $this->parameters;

    }

    public function setRawParameters($parameters) {

        $this->parameters = $parameters;

        return $this;

    }

    public function getParameters() {

        return array_keys($this->parameters);

    }

    public function getParameterType($name) {

        if (isset($this->parameters[$name])) {
            
            return $this->parameters[$name]['type'];
            
        }
            
        return null;

    }

    public function isParameterOptional($name) {

        if (isset($this->parameters[$name])) {
            
            return filter_var($this->parameters[$name]['optional'], FILTER_VALIDATE_BOOLEAN);
            
        }

        return null;

    }

    public function hasParameter($name) {

        return isset($this->parameters[$name]);

    }

    public function addParameter($name, $type, $optional = false) {

        $this->parameters[$name] = array(
            "type"     => $type,
            "optional" => filter_var($optional, FILTER_VALIDATE_BOOLEAN)
        );

        return $this;

    }

    public function removeParameter($name) {

        if (isset($this->parameters[$name])) {
            
            unset($this->parameters[$name]);
            
        }
            
        return $this;

    }

}
