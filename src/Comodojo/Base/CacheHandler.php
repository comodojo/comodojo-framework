<?php namespace Comodojo\Base;

use \Comodojo\Cache\CacheManager;
use \Comodojo\Cache\FileCache;
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

class CacheHandler {
    
    public static function create(Configuration $configuration) {

        $enabled = $configuration->get('cache-enabled');
        
        $algorithm = self::getAlgorithm($configuration->get('cache-algorithm'));
        
        $folder = $configuration->get('local-cache');
        
        $manager = new CacheManager($algorithm);
        
        if ( $enabled === true ) {
            
            $provider = new FileCache($folder);
            
        }

        return $manager;

    }
    
    protected static function getAlgorithm($algo) {

        switch ( strtoupper($algo) ) {

            case 'PICK-LAST':
                $algorithm = CacheManager::PICK_LAST;
                break;

            case 'PICK-RANDOM':
                $algorithm = CacheManager::PICK_RANDOM;
                break;

            case 'PICK-BYWEIGHT':
                $algorithm = CacheManager::PICK_BYWEIGHT;
                break;

            case 'PICK-ALL':
                $algorithm = CacheManager::PICK_ALL;
                break;

            case 'PICK-FIRST':
            default:
                $algorithm = CacheManager::PICK_FIRST;
                break;

        }

        return $algorithm;
        
    }
    
    // protected static function getProvider($provider) {
    //     
    //     switch ( strtolower($provider) ) {
    // 
    //         case 'apc':
    //             $return = "\\Comodojo\\Cache\\ApcCache";
    //             break;
    //   
    //         case 'database':
    //             $return = "\\Comodojo\\Cache\\DatabaseCache";
    //             break;
    // 
    //         case 'filesystem':
    //             $return = "\\Comodojo\\Cache\\FileCache";
    //             break;
    // 
    //         case 'memcached':
    //             $return = "\\Comodojo\\Cache\\MemcachedCache";
    //             break;
    //   
    //         case 'redis':
    //             $return = "\\Comodojo\\Cache\\PhpRedisCache";
    //             break;
    //         
    //         case 'xcache':
    //             $return = "\\Comodojo\\Cache\\XCacheCache";
    //             break;
    // 
    //         default:
    //             $return = null;
    //             break;
    // 
    //     }
    // 
    //     return $return;
    // 
    // }

}
