<?php namespace Comodojo\Configuration;

use \Comodojo\Settings\Setting as FrameworkSetting;
use \Comodojo\Settings\Settings as FrameworkSettings;
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

class Settings extends AbstractConfiguration {

    public function get() {

        $return = new FrameworkSettings($this->getDbh());

        return $return;

    }

    public function getByName($name) {

        $return = new FrameworkSettings($this->getDbh());

        return $return->getElementByName($name);

    }

    public function getById($id) {

        return FrameworkSetting::load($id, $this->getDbh());

    }

    public function getByPackage($package) {

        $return = array();

        $settings = new FrameworkSettings($this->dbh);

        $list = $settings->getListByPackage($package);

        if (!empty($list)) {

            foreach ($list as $name) {

                array_push($return, $settings->getElementByName($name));

            }

        }

        return $return;

    }

    public function add($package, $name, $value) {

        $return = new FrameworkSetting($this->getDbh());

        $return->setName($name)
            ->setPackage($package)
            ->setValue($value)
            ->save();

        return $return;

    }

    public function update($id, $package, $name, $value) {

        $return = FrameworkSetting::load($id, $this->getDbh());

        if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");

        $return->setName($name)
            ->setPackage($package)
            ->setValue($value)
            ->save();

        return $return;

    }

    public function delete($id) {

        $return = FrameworkSetting::load($id, $this->getDbh());

        if ( empty($return) ) throw new ConfigurationException("The specified ID doesn't exist");

        return $return->delete();

    }

}
