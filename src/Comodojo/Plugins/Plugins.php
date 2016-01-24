<?php namespace Comodojo\Plugins;

use \Comodojo\Database\Database;
use \Comodojo\Base\Iterator;
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

class Plugins extends Iterator {

    protected $fw = array();

    public function getElementByName($name) {

        if (!isset($this->data[$name]))
            return null;

        return Plugin::load($this->data[$name], $this->dbh);

    }

    public function getSupportedFrameworks() {

        return array_keys($this->fw);

    }

    public function getListByFramework($fw) {

        return $this->fw[$fw];

    }

    public function removeElementByName($name) {

        if (isset($this->data[$name])) {

            $plugin    = Setting::load($this->data[$name], $this->dbh);

            $framework = $this->getListByFramework($setting->getFramework());

            $index     = array_search($name, $framework);

            array_splice($this->fw[$setting->getFramework()], $index, 1);

            $plugin->delete();

            unset($this->data[$name]);

        }

        return $this;

    }

    protected function loadData() {

        $this->data = array();

        $query = "SELECT * FROM comodojo_plugins ORDER BY name";

        try {

            $result = $this->dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->loadList($result, 'name');

        $this->loadFrameworks($result);

        return $this;

    }

    protected function loadFrameworks($data) {

        if ($data->getLength() > 0) {

            $data = $data->getData();

            foreach ($data as $row) {

                if (!isset($this->fw[$row['framework']]))
                    $this->fw[$row['framework']] = array();

                array_push($this->fw[$row['framework']], $row['name']);

            }

        }

        foreach ($this->fw as $fw => $list) {

            $this->fw[$fw] = sort($list);

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
            json_encode($this->data),
            json_encode($this->fw)
        ));

    }

    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Plugins $this
     */
    public function unserialize($data) {

        $data = unserialize($data);

        $this->data = json_decode($data[0], true);
        $this->fw   = json_decode($data[1], true);

        return $this;

    }

}
