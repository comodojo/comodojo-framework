<?php namespace Comodojo\Base;

use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \Monolog\Handler\SyslogUdpHandler;
use \Monolog\Handler\NullHandler;
use \Monolog\Formatter\LineFormatter;
use \Comodojo\Dispatcher\Components\Configuration;

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

class AuditHandler extends LogHandler {
    
    /**
     * Create the logger
     *
     * @param Configuration $configuration
     *
     * @return Logger
     */
    public static function create(Configuration $configuration) {

        $local = $configuration->get('audit-local');
        
        $remote = $configuration->get('audit-remote');
        
        $logger = new Logger('comodojo-audit');

        if ( is_null($local) && is_null($remote) ) {
            
            $logger->pushHandler( new NullHandler(Logger::INFO) );
            
        } else {
            
            self::parseLocal($logger, $local);
            
            self::parseRemote($logger, $remote);
            
        }
        
        return $logger;

    }

}
