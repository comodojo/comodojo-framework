<?php namespace Comodojo\Base;

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

trait PackagesTrait {

    protected $packages = array();

    public function getPackages() {

        return array_keys($this->packages);

    }

    public function getListByPackage($package) {

        return $this->packages[$package];

    }

    protected function loadPackages($data, $fieldName) {

        if ($data->getLength() > 0) {

            $data = $data->getData();

            foreach ($data as $row) {

                if (!isset($this->packages[$row['package']])) {
                    
                    $this->packages[$row['package']] = array();
                    
                }

                array_push($this->packages[$row['package']], $row[$fieldName]);

            }

        }

        foreach ($this->packages as $package => $list) {

            $this->packages[$package] = sort($list);

        }

        return $this;

    }

}
