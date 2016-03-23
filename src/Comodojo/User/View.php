<?php namespace Comodojo\User;

use \Comodojo\Components\ViewTrait;
use \Comodojo\Components\PackageViewTrait;
use \Comodojo\Authentication\View as AuthenticationView;
use \Comodojo\Role\View as RoleView;
use \Comodojo\Role\Iterator as RoleIterator;
use \Exception;

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

class View extends Model {

    use ViewTrait;
    use PackageViewTrait;

    public function getAuthentication() {

        $auth = new AuthenticationView($this->configuration(), $this->database());

        return $auth->load($this->authentication);

    }

    public function getPrimaryRole() {

        $auth = new RoleView($this->configuration(), $this->database());

        return $auth->load($this->primaryrole);

    }

    public function getRoles() {

        return RoleIterator::loadByUser($this->configuration(), $this->id, $this->database(), false);

    }

}
