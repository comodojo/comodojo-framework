<?php namespace Comodojo\Components;

/**
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

trait CountableTrait {

    /**
     * Check if an offset exists
     *
     * @param string|int $index
     *
     * @return boolean
     */
    public function offsetExists($index) {

        return $this->offsetGet($index) !== null;

    }

    /**
     * Get element from index
     *
     * @param string|int $index
     *
     * @return mixed
     */
    public function offsetGet($index) {

        return $this->data[$index];

    }

    /**
     * Set a value
     *
     * @param string|int $index
     * @param mixed      $value
     */
    public function offsetSet($index, $value) {

        $this->data[$index] = $value;

    }

    /**
     * Remove an element from array
     *
     * @param string|int $index
     *
     * @return boolean
     */
    public function offsetUnset($index) {

        $action = unset($this->data[$index]);

        return $action;

    }

}
