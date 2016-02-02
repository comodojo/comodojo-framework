<?php namespace Comodojo\Configuration;

use \Comodojo\Database\EnhancedDatabase;
use \Comodojo\Exception\DatabaseException;
use \Comodojo\Exception\ConfigurationException;
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

abstract class AbstractConfiguration implements ConfigurationInterface {

    protected $database;

    public function __construct( EnhancedDatabase $database ) {
		
		$this->database = $database;
		
    }
    
    public function add() {
        
        $params = func_get_args();
        
        array_unshift($params, 0);
        
        $this->save($this->loadParameters($params));
        
    }
    
    public function update() {
        
        $params = func_get_args();
        
        $this->save($this->loadParameters($params));
        
    }
    
    protected function loadParameters($params) {
        
        $elaborated = array( "id" => array_shift($params) );
        
        $list = $this->parameters();
        
        $par = 0;
        
        foreach ($list as $name => $default) {
            
            if (!isset($params[$par]) && is_null($default)) {
                
                throw new ConfigurationException("Parameter '$name' must be specified!");
                
            }
            
            $value = (!isset($params[$par]))?$default:$params[$par];
            
            $elaborated[$name] = $value;
            
            $par++;
            
        }
        
        return $elaborated;
        
    }

    public function getByName($name) {

        return $this->get()->getByName($name);

    }

    public function getById($id) {

        return $this->get()->getByID($id);

    }

    public function getByPackage($package) {

        $return = array();

        $iter   = $this->get();

        $list   = $iter->getListByPackage($package);

        if (!empty($list)) {

            foreach ($list as $name) {

                array_push($return, $iter->getByName($name));

            }

        }

        return $return;

    }

    public function delete($id) {

        $return = $this->getById($id);

        if ( empty($return) ) throw new ConfigurationException("The specified ID doesn't exist");
        
        return $this->get()->removeByID($id);

    }

    public function database() {

        return $this->database;

    }

}
