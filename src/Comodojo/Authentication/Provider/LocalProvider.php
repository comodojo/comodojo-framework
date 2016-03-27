<?php namespace Comodojo\Authentication\Provider;

use \Comodojo\Database\EnhancedDatabase;
use \Comodojo\Dispatcher\Components\Configuration;
use \Comodojo\User\View as UserView;
use \Comodojo\Exception\AuthenticationException;
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

class LocalProvider implements AuthenticationProviderInterface {

    private $configuration;

    private $parameters;

    private $database;

    private $user;

    public function __construct(
        Configuration $configuration,
        UserView $user,
        $parameters,
        EnhancedDatabase $database) {

        $this->configuration = $configuration;

        $this->user = $user;

        $this->parameters = $parameters;

        $this->database = $database;

    }

    public function authenticate($password) {

        return password_verify($password, $this->user->password);

    }

    public function passwd($password) {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        return $hash;

    }

    public function chpasswd($old_password, $new_password) {

        if ( $this->authenticate($old_password) === false ) {

            throw new AuthenticationException('Previous password mismatch');

        }

        return $this->passwd($new_password);

    }

    public function release() {

        return true;

    }

}
