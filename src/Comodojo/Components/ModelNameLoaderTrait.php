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

trait ModelNameLoaderTrait {

    public function loadByName($value) {

        $className = getClass($this);

        if ( empty($name) ) {
            throw new Exception("Unable to load object $className: empty name");
        }

        try {

            $result = $this->database
                ->table($this->schema)
                ->keys(array_keys($this->data))
                ->where(self::$element_name, '=', $value)
                ->get();

            if ( $result->getLength() != 1 ) {
                throw new Exception("Unable to load object $className: missing name $value");
            }

            $data = $result->getData();

            $return = $this->populate($data[0]);

        } catch (DatabaseException $de) {
            throw $de;
        } catch (Exception $e) {
            throw $e;
        }

        return $return;

    }

}
