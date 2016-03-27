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

class AliasesController extends AliasesView {

    public function set($aliases) {

        $this->aliases = $aliases;

        return $this;

    }

    public function add($alias) {

        if (!in_array($alias, $this->aliases)) {

            array_push($this->aliases, $alias);

            sort($this->aliases);

        }

        return $this;

    }

    public function remove($alias) {

        if (in_array($alias, $this->aliases)) {

            $idx = array_search($alias, $this->aliases);

            array_splice($this->aliases, $idx, 1);

        }

        return $this;

    }

}
