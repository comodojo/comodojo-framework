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

class LogHandler {
    
    /**
     * Create the logger
     *
     * @param Configuration $configuration
     *
     * @return Logger
     */
    public static function create(Configuration $configuration) {

        $local = $configuration->get('log-local');
        
        $remote = $configuration->get('log-remote');
        
        $logger = new Logger('comodojo-log');

        if ( is_null($local) && is_null($remote) ) {
            
            $logger->pushHandler( new NullHandler(Logger::ERROR) );
            
        } else {
            
            self::parseLocal($logger, $local);
            
            self::parseRemote($logger, $remote);
            
        }
        
        return $logger;

    }
    
    protected static function parseLocal(Logger $logger, $conf) {
        
        $local = explode("::", $conf);
        
        if ( sizeof($local) != 2 ) return;
        
        $filename = $local[0];
        
        $level = self::getLevel($local[1]);
        
        $handler = new StreamHandler($filename, $level);
        
        $logger->pushHandler($handler);
        
    }
    
    protected static function parseRemote(Logger $logger, $conf) {
        
        $remote = explode("::", $conf);
        
        if ( sizeof($remote) != 4 ) return;
        
        $host = $remote[0];
        
        $port = $remote[1];
        
        $facility = self::getFacility($remote[2]);
        
        $level = self::getLevel($remote[3]);
        
        $handler = new SyslogUdpHandler($host, $port, $facility, $level);
        
        $formatter = new LineFormatter("%datetime% - %channel% - %level_name% - %message%\n");

        $handler->setFormatter($formatter);
        
        $logger->pushHandler($handler);
        
    }

    /**
     * Map provided log level to level code
     *
     * @param   string    $level
     *
     * @return  integer
     */
    protected static function getLevel($level) {

        switch ( strtoupper($level) ) {

            case 'INFO':
                $logger_level = Logger::INFO;
                break;

            case 'NOTICE':
                $logger_level = Logger::NOTICE;
                break;

            case 'WARNING':
                $logger_level = Logger::WARNING;
                break;

            case 'ERROR':
                $logger_level = Logger::ERROR;
                break;

            case 'CRITICAL':
                $logger_level = Logger::CRITICAL;
                break;

            case 'ALERT':
                $logger_level = Logger::ALERT;
                break;

            case 'EMERGENCY':
                $logger_level = Logger::EMERGENCY;
                break;

            case 'DEBUG':
            default:
                $logger_level = Logger::DEBUG;
                break;

        }

        return $logger_level;

    }
    
    /**
     * Map provided log facilities to monolog code
     *
     * @param   string    $facility
     *
     * @return  integer
     */
    protected static function getFacility($facility) {
        
        switch ( strtolower($facility) ) {

            case 'auth':
                $logger_facility = LOG_AUTH;
                break;

            case 'authpriv':
                $logger_facility = LOG_AUTHPRIV;
                break;

            case 'cron':
                $logger_facility = LOG_CRON;
                break;

            case 'daemon':
                $logger_facility = LOG_DAEMON;
                break;

            case 'kern':
                $logger_facility = LOG_KERN;
                break;
                
            case 'local0':
                $logger_facility = LOG_LOCAL0;
                break;

            case 'lpr':
                $logger_facility = LOG_LPR;
                break;

            case 'mail':
                $logger_facility = LOG_MAIL;
                break;

            case 'news':
                $logger_facility = LOG_NEWS;
                break;

            case 'user':
                $logger_facility = LOG_USER;
                break;
                
            case 'uucp':
                $logger_facility = LOG_UUCP;
                break;                

            case 'syslog':
            default:
                $logger_facility = LOG_SYSLOG;
                break;

        }

        return $logger_facility;

    }

}
