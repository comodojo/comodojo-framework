<?php namespace Comodojo\Configuration;

use \Comodojo\Rpc\RpcMethod as FrameworkRpcMethod;
use \Comodojo\Rpc\Rpc as FrameworkRpc;
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

class Rpc extends AbstractConfiguration {

    public function get() {

        $return = new FrameworkRpc($this->getDbh());

        return $return;

    }

    public function getByName($name) {

        $return = new FrameworkRpc($this->getDbh());

        return $return->getElementByName($name);

    }

    public function getById($id) {

        return FrameworkRoutes::load($id, $this->getDbh());

    }

    public function add($package, $name, $callback, $method, $description = "", $signatures = array()) {

        $return = new FrameworkRpcMethod($this->getDbh());

        $return->setName($name)
            ->setPackage($package)
            ->setCallback($class)
            ->setMethod($method)
            ->setDescription($description)
            ->setRawSignatures($signatures)
            ->save();

        return $return;

    }

    public function update($id, $package, $name, $callback, $method, $description = "", $signatures = array()) {

        $return = FrameworkRpcMethod::load($id, $this->getDbh());

        if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");

        $return->setName($name)
            ->setPackage($package)
            ->setCallback($class)
            ->setMethod($method)
            ->setDescription($description)
            ->setRawSignatures($signatures)
            ->save();

        return $return;

    }

    public function delete($id) {

        $return = FrameworkRpcMethod::load($id, $this->getDbh());

        if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");

        return $return->delete();

    }

}
