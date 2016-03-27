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

class OptionsView {

    protected $options = array();

    public function __construct($options) {

        $this->options = $options;

    }

    public function get() {

        return array_keys($this->options);

    }

    public function getRaw() {

        return $this->options;

    }

    public function exists($name) {

        return isset($this->options[$name]);

    }

    public function getShortName($name) {

        if (isset($this->options[$name])) {

            return $this->options[$name]['short_name'];

        }

        return null;

    }

    public function getLongName($name) {

        if (isset($this->options[$name])) {

            return $this->options[$name]['long_name'];

        }

        return null;

    }

    public function getAction($name) {

        if (isset($this->options[$name])) {

            return $this->options[$name]['action'];

        }

        return null;

    }

    public function getDescription($name) {

        if (isset($this->options[$name])) {

            return $this->options[$name]['description'];

        }

        return null;

    }

}
