<?php namespace Comodojo\Authentication;

use \Lcobucci\JWT\Token;
use \Lcobucci\JWT\Builder;
use \Lcobucci\JWT\Signer\Hmac\Sha256;
use \Lcobucci\JWT\Parser;
use \Comodojo\Dispatcher\Components\Configuration;
use \Comodojo\Database\EnhancedDatabase;
use \Comodojo\User\Iterator as UserIterator;
use \Comodojo\User\View as UserView;
use \Comodojo\Exception\AuthenticationException;
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

class Broker {

    private $database;

    private $configuration;

    public function __construct(Configuration $configuration, EnhancedDatabase $database) {

        $this->configuration = $configuration;

        $tthis->database = $database;

    }

    public function authenticate($username, $password) {

        $filter = array("username","=",$username);

        $users = UserIterator::loadBy($this->configuration, $filter, $this->database);

        if ( count($users) != 1 ) {

            throw new AuthenticationException("Unknown user or wrong password");

        }

        $user = $users[0];

        if ( $user->enabled === false ) {

            throw new AuthenticationException("Account locked, please contact administrator.");

        }

        if ( $user->getAuthentication()->getProvider()->authenticate($user, $password) !== true ) {

            throw new AuthenticationException("Unknown user or wrong password");

        }

        return $this->generateToken($user);

    }

    public function validate($token_string) {

        $signer = new Sha256();

        $token = (new Parser())->parse((string) $token_string);

        if ( $token->validate($signer, $this->configuration->get('auth-key')) === false ) {

            throw new AuthenticationException("Token mismatch!");

        }

        $id = $token->getClaim('uid');

        $user = new UserView($this->configuration, $this->database);

        $user->load($id);

        if ( $user->enabled() === false ) {

            throw new AuthenticationException("Account locked, please contact administrator.");

        }

        return $user;

    }

    public function release(UserView $user) {

        $filter = array("username","=",$username);

        $users = UserIterator::loadBy($this->configuration, $filter, $this->database);

        if ( count($users) != 1 ) {

            throw new AuthenticationException("Unknown user or wrong password");

        }

        $user = $users[0];

        if ( $user->enabled === false ) {

            throw new AuthenticationException("Account locked, please contact administrator.");

        }

        if ( $user->getAuthentication()->getProvider()->release($user) !== true ) {

            throw new AuthenticationException("Unknown user or wrong password");

        }

        return true;

    }

    private function generateToken(UserView $user) {

        $signer = new Sha256();

        $issuedAt = time();

        $key = $this->configuration->get('auth-key');
        $ttl = $this->configuration->get('auth-ttl');

        $expiration = is_null($ttl) ? (int)$ttl : 3600;

        $builder = new Builder();

        $builder->setIssuedAt($issuedAt)
            ->setNotBefore($issuedAt + 1)
            ->setExpiration($issuedAt + $expiration)
            ->set('uid', $user->getId());

        $issuer = $this->configuration->get('auth-issuer');
        $audience = $this->configuration->get('auth-audience');

        if ( $issuer != null ) $builder->setIssuer($issuer);
        if ( $audience != null ) $builder->setAudience($audience);


        $token = $builder->sign($signer, $key)->getToken();

        return (string)$token;

    }

}
