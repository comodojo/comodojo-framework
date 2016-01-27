<?php namespace Comodojo;

use \Comodojo\Database\Database;
use \Comodojo\Users\User;
use \Comodojo\Cookies\Cookie;

use \Comodojo\Exception\AuthenticationException;
use \Comodojo\Exception\DatabaseException;
use \Comodojo\Exception\CookieException;


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

class Comodojo {

    private $configuration;
    
    private $dbh;
    
    private $logger;
    
    private $cache;
    
    private $dispatcher;
    
    public function __construct() {
        
    }
    
    public function dbh() {
        
        return $this->dbh;
        
    }
    
    public function logger() {
        
        return $this->logger;
        
    }
    
    public function cache() {
        
        return $this->cache;
        
    }
    
    public function dispatcher() {
        
        return $this->dispatcher;
        
    }
    
    public function boot() {
        
        try {
            
            $token = Cookie::retrieve('comodojo-auth-token');
            
            $broker = new Broker($this->dbh);
            
            $user = $broker->validate($token);
            
        } catch (AuthenticationException $ae) {
            
            $user = User::loadGuest();
            
        } catch (CookieException $ce ) {
            
            $user = User::loadGuest();
            
        }
        
        return $this->dispatcher()->dispatch();
        
    }

}
