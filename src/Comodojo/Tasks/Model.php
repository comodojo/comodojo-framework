<?php namespace Comodojo\Tasks;

use \Comodojo\Database\EnhancedDatabase;
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

class Model {

    public static function load(EnhancedDatabase $database, $id) {
        
        try {
            
            $result = $database
                ->table('tasks')
                ->keys('*')
                ->where('id', '=', $id)
                ->get();

        } catch (DatabaseException $de) {

            throw $de;

        }
        
        return $result;
        
    }

    public static function create(EnhancedDatabase $database, $name, $class, $description, $package) {
        
        try {
            
            $result = $database
                ->table('tasks')
                ->keys(array('name', 'class', 'description', 'package'))
                ->values(array($name, $class, $description, $package))
                ->store();

        } catch (DatabaseException $de) {

            throw $de;

        }
        
        return $result;
        
    }
    
    public static function update(EnhancedDatabase $database, $id, $name, $class, $description, $package) {
        
        try {
            
            $result = $database
                ->table('tasks')
                ->keys(array('name', 'class', 'description', 'package'))
                ->values(array($name, $class, $description, $package))
                ->where('id', '=', $id)
                ->update();

        } catch (DatabaseException $de) {

            throw $de;

        }
        
        return $result;
        
    }
    
    public static function delete(EnhancedDatabase $database, $id) {
        
        try {
            
            $result = $database
                ->table('tasks')
                ->where('id', '=', $id)
                ->delete();

        } catch (DatabaseException $de) {

            throw $de;

        }
        
        return $result;
        
    }
    
}