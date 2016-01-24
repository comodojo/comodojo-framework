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

abstract class ConfigIterator implements \Iterator, \ArrayAccess, \Countable, \Serializable {

    protected $data = array();

    protected $current = 0;

    protected $dbh;

    public function __construct(Database $dbh) {

        $this->dbh = $dbh;

        $this->loadData();

    }

    abstract protected function loadData();

    abstract public function getElementByName($name);

    abstract public function removeElementByName($name);

    public function getList() {

        return array_keys($this->data);

    }

    public function addElement($name, $element) {

        $element->setName($name)->save();

        $this->data[$name] = $element->getID();

    }

    protected function loadList($data, $fieldName) {

        if ($data->getLength() > 0) {

            $data = $data->getData();

            foreach ($data as $row) {

                $this->data[$row[$fieldName]] = intval($row['id']);

            }

        }

    }

    /**
     * The following methods implement the Iterator interface
     */

    /**
     * Reset the iterator
     *
     * @return Routes $this
     */
    public function rewind() {

        $this->current  = 0;

        return $this;

    }

    /**
     * Return the current route description
     *
     * @return string $description
     */
    public function current() {

        return $this->getElementByName($this->data[$this->current]);

    }

    /**
     * Return the current route name
     *
     * @return string $name
     */
    public function key() {

        return $this->data[$this->current];

    }

    /**
     * Fetch the iterator
     *
     * @return Routes $this
     */
    public function next() {

        $this->current++;

        return $this;

    }

    /**
     * Check if there's a next description
     *
     * @return boolean $hasNext
     */
    public function valid() {

        return isset($this->data[$this->current]);

    }

    /**
     * The following methods implement the ArrayAccess interface
     */

    /**
     * Check if an offset exists
     *
     * @param string $name
     *
     * @return boolean $hasRoute
     */
    public function offsetExists($name) {

        return isset($this->data[$name]);

    }

    /**
     * Get a route description
     *
     * @param string $name
     *
     * @return string $description
     */
    public function offsetGet($name) {

        return $this->getElementByName($name);

    }

    /**
     * Set a route
     *
     * @param string $name
     * @param ConfigElement $element
     *
     * @return ConfigIterator $this
     */
    public function offsetSet($name,  $element) {

        $this->addElement($name, $element);

        return $this;

    }

    /**
     * Remove a route
     *
     * @param string $name
     *
     * @return ConfigIterator $this
     */
    public function offsetUnset($name) {

        return $this->removeElementByName($name);

    }

    /**
     * The following methods implement the Countable interface
     */

    /**
     * Return the amount of routes loaded
     *
     * @return int $count
     */
    public function count() {

        return count($this->data);

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

        return serialize(
            $this->data
        );

    }

    /**
     * Return the unserialized object
     *
     * @param string $data Serialized data
     *
     * @return Routes $this
     */
    public function unserialize($data) {

        $this->data = unserialize($data);

        return $this;

    }

}
