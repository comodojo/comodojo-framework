<?php namespace Comodojo\Command;

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

class ArgumentsView {

    protected $arguments = array();

    public function __construct($arguments) {

        $this->arguments = $arguments;

    }

    public function get() {

        return array_keys($this->arguments);

    }

    public function getRaw() {

        return $this->arguments;

    }

    public function exists($argument) {

        return isset($this->arguments[$argument]);

    }

    public function getChoices($argument) {

        if (isset($this->arguments[$argument])) {

            return $this->arguments[$argument]['choices'];

        }

        return null;

    }

    public function getMultipleValues($argument) {

        if (isset($this->arguments[$argument])) {

            return $this->arguments[$argument]['multiple'];

        }

        return null;

    }

    public function getOptional($argument) {

        if (isset($this->arguments[$argument])) {

            return $this->arguments[$argument]['optional'];

        }

        return null;

    }

    public function getHelpName($argument) {

        if (isset($this->arguments[$argument])) {

            return $this->arguments[$argument]['help_name'];

        }

        return null;

    }

    public function getDescription($argument) {

        if (isset($this->arguments[$argument])) {

            return $this->arguments[$argument]['description'];

        }

        return null;

    }

}
