<?php namespace Comodojo\Configuration;

use \Comodojo\Plugins\Plugin as FrameworkPlugin;
use \Comodojo\Plugins\Plugins as FrameworkPlugins;
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

class Plugins extends AbstractConfiguration {

    public function get() {

        $return = new FrameworkPlugins($this->getDbh());

        return $return;

    }

    public function getByName($name) {

        $return = new FrameworkPlugins($this->getDbh());

        return $return->getElementByName($name);

    }

    public function getById($id) {

        return FrameworkPlugin::load($id, $this->getDbh());

    }

    public function getByFramework($framework) {

        $return = array();

        $plugins = new FrameworkPlugins($this->dbh);

        $list = $plugins->getListByFramework($framework);

        if (!empty($list)) {

            foreach ($list as $name) {

                array_push($return, $plugins->getElementByName($name));

            }

        }

        return $return;

    }

    public function add($package, $framework, $name, $class, $method = "", $event = "") {

        $return = new FrameworkPlugin($this->getDbh());

        $return->setName($name)
            ->setPackage($package)
            ->setFramework($framework)
            ->setClass($class)
            ->setMethod($method)
            ->setEvent($event)
            ->save();

        return $return;

    }

    public function update($id, $package, $framework, $name, $class, $method = "", $event = "") {

        $return = FrameworkPlugin::load($id, $this->getDbh());

        if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");

        $return->setName($name)
            ->setPackage($package)
            ->setFramework($framework)
            ->setClass($class)
            ->setMethod($method)
            ->setEvent($event)
            ->save();

        return $return;

    }

    public function delete($id) {

        $return = FrameworkPlugin::load($id, $this->getDbh());

        if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");

        return $return->delete();

    }

}
