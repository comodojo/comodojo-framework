<?php namespace Comodojo\Authentication\Provider;

use \Comodojo\Database\Database;
use \Comodojo\Users\User;
use \Comodojo\Exception\AuthenticationException;
use \Comodojo\Exception\DatabaseException;

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

    private $user;
    
    private $dbh;

    public function __construct(User $user, Database $database) {
        
        $this->user = $user;
        
        $this->dbh = $database;
        
    }
    
    public function authenticate($password) {
        
        return password_verify($password, $user->getPassword());
        
    }
    
    public function passwd($password) {
        
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $query = sprintf("UPDATE comodojo_users SET `password` = %s WHERE id = %d",
            mysqli_real_escape_string($this->dbh->getHandler(), $hash),
            $this->user->getId()
        );

        try {

            $this->dbh->query($query);
            
        } catch (DatabaseException $de) {

            throw $de;

        }

        return $this;

    }
    
    public function chpasswd($old_password, $new_password) {
        
        if ( $this->authenticate($old_password) === false ) {
            
            throw new AuthenticationException('Previous password mismatch');
            
        }
        
        $this->passwd($new_password);
        
        return $this;
        
    }

}
