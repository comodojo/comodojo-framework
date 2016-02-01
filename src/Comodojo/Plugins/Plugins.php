<?php namespace Comodojo\Plugins;

use \Comodojo\Base\Iterator;
use \Comodojo\Base\PackagesTrait;
use \Comodojo\Base\Element;
use \Comodojo\Database\QueryResult;

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

    use PackagesTrait;

    protected $frameworks = array();

    public function getByID($id) {

		return Plugin::load($this->database, intval($id));

	}

	protected function loadData() {

        $data = $this->loadFromDatabase("plugins", "name");

        $this->loadFrameworks($data);

    }

    public function getSupportedFrameworks() {

        return array_keys($this->frameworks);

    }

    public function getListByFramework($framework) {

        return $this->frameworks[$framework];

    }

    protected function remove(Element $element) {

        $name = $element->getName();

        $framework = $this->getListByFramework($element->getFramework());

        $index = array_search($name, $framework);

        array_splice($this->frameworks[$val->getFramework()], $index, 1);

        return parent::remove($element);

    }

    protected function loadFrameworks(QueryResult $resultset) {

        if ( $resultset->getLength() > 0 ) {

            $data = $resultset->getData();

            foreach ($data as $row) {

                if ( !isset($this->frameworks[$row['framework']]) ) {

                    $this->frameworks[$row['framework']] = array();

                }

                array_push($this->frameworks[$row['framework']], $row['name']);

            }

        }

        foreach ($this->frameworks as $fw => $list) {

            $this->frameworks[$fw] = sort($list);

        }

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
            json_encode($this->packages),
            json_encode($this->frameworks),
            json_encode($this->current)
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
        $this->packages = json_decode($data[1], true);
        $this->frameworks = json_decode($data[2], true);
        $this->current = json_decode($data[3], true);

        return $this;

    }

}
