<?php namespace Comodojo\User;

use \Comodojo\Components\ControllerTrait;
use \Comodojo\Components\PackageControllerTrait;
use \Comodojo\Authentication\Controller as AuthenticationController;
use \Comodojo\Role\Controller as RoleController;
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

class Controller extends View {

    use ControllerTrait;
    use PackageControllerTrait;

    public function persist() {

        if ( $this->id == 0 ) {

            // a new user is being created, the password will be generated also.
            $this->password = $this->getAuthentication()->getProvider($this)->passwd($this->password);

            return $this->create();

        } else {

            /* an existing user is being updated, we do not distinguish here if
             * is an administrative action or user wish.
             * @todo: design a strong pwd update flow
             */

            return $this->update();

        }

    }

    public function delete() {

        return $this->remove();

    }

    public function getAuthentication() {

        $auth = new AuthenticationController($this->configuration(), $this->database());

        return $auth->load($this->authentication);

    }

    public function getPrimaryRole() {

        $auth = new RoleController($this->configuration(), $this->database());

        return $auth->load($this->primaryrole);

    }

    public function getRoles() {

        return RoleIterator::loadByUser($this->configuration(), $this->id, $this->database(), true);

    }

    public function addRole($role_or_id) {

        if ( $role_or_id instanceof RoleView ) $id = $role_or_id->id;
        else $id = $role_or_id;

        return $this->pushRole($id);

    }

    public function deleteRole($role_or_id) {

        if ( $role_or_id instanceof RoleView ) $id = $role_or_id->id;
        else $id = $role_or_id;

        return $this->popRole($id);

    }

}
