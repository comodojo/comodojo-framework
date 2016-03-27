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

class ArgumentsController extends ArgumentsView {

    public function set($arguments) {

        $this->arguments = array();

        foreach ($arguments as $name => $arg) {
            $this->add($name, $arg['choices'], $arg['multiple'], $arg['optional'], $arg['description']);
        }

        return $this;

    }

    public function add(
        $argument,
        $choices = array(),
        $multiple = false,
        $optional = false,
        $description = null,
        $help_name = null) {

        if (!isset($this->arguments[$argument])) {

            $this->arguments[$argument] = array(
                'choices'     => $choices,
                'multiple'    => $multiple,
                'optional'    => $optional,
                'description' => $description,
                'help_name'   => $help_name
            );

        }

        return $this;

    }

    public function remove($argument) {

        if (isset($this->arguments[$argument])) {

            unset($this->arguments[$argument]);

        }

        return $this;

    }

    public function setChoices($argument, $value) {

        if (isset($this->arguments[$argument])) {

            $this->arguments[$argument]['choices']   = $value;

        }

        return $this;

    }

    public function setMultipleValues($argument, $value) {

        if (isset($this->arguments[$argument])) {

            $this->arguments[$argument]['multiple'] = $value;

        }

        return $this;

    }

    public function setOptional($argument, $value) {

        if (isset($this->arguments[$argument])) {

            $this->arguments[$argument]['optional'] = $value;

        }

        return $this;

    }

    public function setHelpName($argument, $value) {

        if (isset($this->arguments[$argument])) {

            $this->arguments[$argument]['help_name'] = $value;

        }

        return $this;

    }

    public function setDescription($argument, $value) {

        if (isset($this->arguments[$argument])) {

            $this->arguments[$argument]['description'] = $value;

        }

        return $this;

    }

}
