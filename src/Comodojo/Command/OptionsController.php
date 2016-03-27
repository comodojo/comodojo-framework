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

class OptionsController extends OptionsView {

    public function set($options) {

        $this->options = array();

        foreach ($options as $name => $option) {

            $this->add($name, $option['short_name'], $option['long_name'], $option['action'], $option['description']);

        }

        return $this;

    }

    public function add($name, $short = "", $long = "", $action = "StoreTrue", $description = "") {

        if ( empty($short) ) $short = "-"  . substr($name, 0, 1);
        if ( empty($long) )  $long  = "--" . $name;

        if ( !isset($this->options[$name]) ) {

            $this->options[$name] = array(
                'short_name'  => $short,
                'long_name'   => $long,
                'action'      => $action,
                'description' => $description
            );

        }

        return $this;

    }

    public function remove($name) {

        if (isset($this->options[$name])) {

            unset($this->options[$name]);

        }

        return $this;

    }

    public function setShortName($option, $value) {

        if (isset($this->options[$option])) {

            $this->options[$option]['short_name'] = $value;

        }

        return $this;

    }

    public function setLongName($option, $value) {

        if (isset($this->options[$option])) {

            $this->options[$option]['long_name'] = $value;

        }

        return $this;

    }

    public function setAction($option, $value) {

        if (isset($this->options[$option])) {

            $this->options[$option]['action'] = $value;

        }

        return $this;

    }

    public function setDescription($option, $value) {

        if (isset($this->options[$option])) {

            $this->options[$option]['description'] = $value;

        }

        return $this;

    }

}
