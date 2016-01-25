<?php namespace Comodojo\Base;

use \Comodojo\Database\Database;
use \Iterator as PhpIterator;
use \ArrayAccess;
use \Countable;
use \Serializable;
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

abstract class Iterator implements PhpIterator, ArrayAccess, Countable, Serializable {

    protected $data     = array();

    protected $packages = array();

    protected $current  = 0;

    protected $dbh;

    public function __construct(Database $dbh) {

        $this->dbh = $dbh;

        $this->loadData();

    }

    public function getPackages() {

        return array_keys($this->packages);

    }

    public function getListByPackage($package) {

        return $this->packages[$package];

    }

    abstract protected function loadData();

    abstract public function getElementByID($id);

	public function getElementByName($name) {

		if (!isset($this->data[$name]))
			throw new ConfigurationException("Value identified by $name does not exist");

		return $this->getElementByID($this->data[$name], $this->dbh);

	}

    public function getList() {

        return array_keys($this->data);

    }

    public function addElement($name, $element) {

        $element->setName($name)->save();

        $this->data[$name] = $element->getID();

    }

    public function removeElementByName($name) {

        if (isset($this->data[$name])) {

            $val = $this->getElementByName($name);
            
            $this->removeElement($val);

        }

        return $this;

    }

    public function removeElementByID($id) {

        if (in_array($id, array_values($this->data))) {

            $val = $this->getElementByID($id);
            
            $this->removeElement($val);

        }

        return $this;

    }
    
    protected function removeElement($element) {
        
        $name    = $element->getName();
        
        $package = $this->getListByPackage($element->getPackageName());

        $index   = array_search($name, $package);
        
        if ($index >= 0) {

            array_splice($this->packages[$element->getPackageName()], $index, 1);

            $element->delete();

            unset($this->data[$name]);
        
        } else {
            
            throw new ConfigurationException("Unable to remove object");
            
        }
        
    }
    
    protected function loadFromDatabase($table, $fieldName) {

        $this->data = array();

        $query = sprintf("SELECT * FROM %s ORDER BY %s", $table, $fieldName);

        try {

            $result = $this->dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->loadList($result, $fieldName);

        $this->loadPackages($result, $fieldName);

        return $this;
        
    }

    protected function loadList($data, $fieldName) {

        if ($data->getLength() > 0) {

            $data = $data->getData();

            foreach ($data as $row) {

                $this->data[$row[$fieldName]] = intval($row['id']);

            }

        }

    }

    protected function loadPackages($data, $fieldName) {

        if ($data->getLength() > 0) {

            $data = $data->getData();

            foreach ($data as $row) {

                if (!isset($this->packages[$row['package']]))
                    $this->packages[$row['package']] = array();

                array_push($this->packages[$row['package']], $row[$fieldName]);

            }

        }

        foreach ($this->packages as $package => $list) {

            $this->packages[$package] = sort($list);

        }

        return $this;

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

        return serialize(array(
            json_encode($this->data),
            json_encode($this->packages),
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

        $this->data     = json_decode($data[0], true);
        $this->packages = json_decode($data[1], true);
        $this->current  = json_decode($data[2], true);

        return $this;

    }

}
