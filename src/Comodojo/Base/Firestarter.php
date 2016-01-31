<?php namespace Comodojo\Base;

use \Comodojo\Settings\Settings;
use \Comodojo\Database\Database;
use \Comodojo\Database\EnhancedDatabase;
use \Comodojo\Dispatcher\Components\Configuration;
use \Comodojo\Cache\FileCache;
use \Comodojo\Exception\DatabaseException;
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

trait Firestarter {

    private $database;
    
    private $configuration;

    public function database() {
        
        return $this->database;
        
    }
    
    public function configuration() {
        
        return $this->configuration;
        
    }

    protected static function getDatabase() {
        
        if ( self::checkBasicConfiguration() === false ) {
            
            throw new Exception("Cannot read basic configuration file, aborting startup");
            
        }
        
        try {
            
            $db = new Database(
                COMODOJO_DATABASE_MODEL,
                COMODOJO_DATABASE_HOST,
                COMODOJO_DATABASE_PORT,
                COMODOJO_DATABASE_NAME,
                COMODOJO_DATABASE_USER,
                COMODOJO_DATABASE_PASS
            );
            
            $db->tablePrefix(EXTENDER_DATABASE_PREFIX)->autoClean();
            foreach ( $completed_processes as $process ) {
                $db->table(EXTENDER_DATABASE_TABLE_JOBS)
                    ->keys("lastrun")
                    ->values($process[3])
                    ->where('id', '=', $process[6])
                    ->update();
            }
            
        } catch (DatabaseException $de) {
            
            throw $de;
            
        }
        
        return $db;
        
    }
    
    protected static function getEnhancedDatabase() {
        
        if ( self::checkBasicConfiguration() === false ) {
            
            throw new Exception("Cannot read basic configuration file, aborting startup");
            
        }
        
        try {
            
            $db = new EnhancedDatabase(
                COMODOJO_DATABASE_MODEL,
                COMODOJO_DATABASE_HOST,
                COMODOJO_DATABASE_PORT,
                COMODOJO_DATABASE_NAME,
                COMODOJO_DATABASE_USER,
                COMODOJO_DATABASE_PASS
            );
            
            if ( defined(COMODOJO_DATABASE_PREFIX) ) {
                
                $db->tablePrefix(COMODOJO_DATABASE_PREFIX);
                
            }
            
            $db->autoClean();
            
        } catch (DatabaseException $de) {
            
            throw $de;
            
        }
        
        return $db;
        
    }
    
    protected static function getConfiguration(Database $database, $static_configuration = array()) {
        
        if (
            isset( $static_configuration['comodojo-startup-cache'] ) &&
            isset( $static_configuration['dispatcher-cache-folder'] ) &&
            isset( $static_configuration['comodojo-startup-cache-ttl'] ) &&
            $static_configuration['comodojo-startup-cache'] === true
        ) {
            
            $cache = new FileCache($static_configuration['dispatcher-cache-folder']);
            
            $configuration = $cache->setNamespace('COMODOJOSTARTUP')->get('config');
            
            return $configuration;

        }
        
        $configuration = new Configuration();
        
        $settings = new Settings($database);
        
        foreach ( $settings as $setting => $value ) {
            
            $configuration->set($setting, $value);
            
        }
        
        foreach ( $static_configuration as $setting => $value ) {
            
            $configuration->set($setting, $value);
            
        }
        
        if (
            isset( $static_configuration['comodojo-startup-cache'] ) &&
            isset( $static_configuration['dispatcher-cache-folder'] ) &&
            isset( $static_configuration['comodojo-startup-cache-ttl'] ) &&
            $static_configuration['comodojo-startup-cache'] === true
        ) {
            
            $cache->setNamespace('COMODOJOSTARTUP')->set('config', $configuration, $static_configuration['comodojo-startup-cache-ttl']);

        }
        
        return $configuration;
        
    }
    
}