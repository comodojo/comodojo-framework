<?php namespace Comodojo\Authentication;

use \Lcobucci\JWT\Token;
use \Lcobucci\JWT\Builder;
use \Lcobucci\JWT\Signer\Hmac\Sha256;
use \Lcobucci\JWT\Parser;
use \Comodojo\Database\Database;
use \Comodojo\Users\Users;
use \Comodojo\Users\User;
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

class Broker {
    
    private $dbh;
    
    public function __construct(Database $database) {
        
        $this->dbh = $database;
        
    }
    
    public function authenticate($username, $password) {
     
        $users = new Users();
        
        $user = $users->getElementByName($username);
        
        if ( $user === null ) {
            
            throw new AuthenticationException("Unknown user or wrong password");
            
        }
        
        if ( $user->getEnabled() === false ) {
            
            throw new AuthenticationException("Account locked, please contact administrator.");
            
        }
        
        if ( $user->getAuthentication()->getInstance()->authenticate($password) !== true ) {
            
            throw new AuthenticationException("Unknown user or wrong password");
            
        }
        
        return $this->generateToken($user);
        
    }
    
    public function validate($token_string) {
        
        $signer = new Sha256();
        
        $token = (new Parser())->parse((string) $token_string);
        
        if ( $token->validate($signer, COMODOJO_AUTH_KEY) === false ) {
            
            throw new AuthenticationException("Token mismatch!");
            
        }
        
        $id = $token->getClaim('uid');
        
        $user = User::load($id, $this->database);
        
        if ( $user->getEnabled() === false ) {
            
            throw new AuthenticationException("Account locked, please contact administrator.");
            
        }
        
        return $user;
        
    }
    
    public function release($username) {
        
        $users = new Users();
        
        $user = $users->getElementByName($username);
        
        if ( $user === null ) {
            
            throw new AuthenticationException("Unknown user or wrong password");
            
        }
        
        if ( $user->getEnabled() === false ) {
            
            throw new AuthenticationException("Account locked, please contact administrator.");
            
        }
        
        if ( $user->getAuthentication()->getInstance()->release() !== true ) {
            
            throw new AuthenticationException("Unknown user or wrong password");
            
        }
        
        return true;
        
    }
    
    private function generateToken(User $user) {
        
        $signer = new Sha256();
        
        $issuedAt = time();
        
        $expiration = defined('COMODOJO_AUTH_TTL') ? (int)COMODOJO_AUTH_TTL : 3600;

        $builder = new Builder();
        
        $builder->setIssuedAt($issuedAt)
            ->setNotBefore($issuedAt + 1)
            ->setExpiration($issuedAt + $expiration)
            ->set('uid', $user->getId());
        
        if ( defined('COMODOJO_AUTH_ISSUER') ) $builder->setIssuer(COMODOJO_AUTH_ISSUER);
        if ( defined('COMODOJO_AUTH_AUDIENCE') ) $builder->setIssuer(COMODOJO_AUTH_AUDIENCE);
        
        $token = $builder->sign($signer, COMODOJO_AUTH_KEY)->getToken();
            
        return (string)$token;
        
    }
    
}