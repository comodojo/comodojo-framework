<?php namespace Comodojo\Roles;

use \Comodojo\Base\Iterator;
use \Comodojo\Applications\Application;
use \Comodojo\Users\User;
use \Comodojo\Database\EnhancedDatabase;
use \Comodojo\Database\DatabaseException;
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

class Roles extends Iterator {

	public function getByID($id) {

		return Role::load($this->database, intval($id));

	}
	
	public function loadByApplication($application_or_id) {
	    
	    if ( $application_or_id instanceof Application ) {
	        
	        $id = $application_or_id->getID();
	        
	    } else if ( is_int($application_or_id) ) {
	        
	        $id = $application_or_id;
	        
	    } else {
	        
	        throw new Exception("Invalid application object or id");
	        
	    }
	    
	    try {
	        
	        $return = $this->loadDataByApplication($id);    
	        
	    } catch (Exception $e) {
	        
	        throw $e;
	        
	    }
	    
	    return $return;
	    
	}
	
	public function loadByUser($urser_or_id) {
	    
	    if ( $urser_or_id instanceof User ) {
	        
	        $id = $urser_or_id->getID();
	        
	    } else if ( is_int($urser_or_id) ) {
	        
	        $id = $urser_or_id;
	        
	    } else {
	        
	        throw new Exception("Invalid user object or id");
	        
	    }
	    
	    try {
	        
	        $return = $this->loadDataByUser($id);    
	        
	    } catch (Exception $e) {
	        
	        throw $e;
	        
	    }
	    
	    return $return;
	    
	}

    protected function loadData() {

        $this->loadFromDatabase("roles", "name");

    }
    
    protected function loadDataByApplication($id) {
        
        // reset data object
        $this->data = array();

        try {
            
            $result = $this->database
                ->table('roles')
                ->table('applications_to_roles')
                ->keys('*')
                ->where('*_DBPREFIX_*roles.id', '=', '*_DBPREFIX_*applications_to_roles.role')
                ->andWhere('*_DBPREFIX_*applicationss_to_roles.application', '=', $id)
                ->orderBy('name')
                ->get();
                
        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->loadList($result, 'name');
        
        if ( isset( $this->packages ) ) {
            
            $this->loadPackages($result, 'name');
            
        }
        
        return $result;
        
    }
    
    protected function loadDataByUser($id) {
        
        // reset data object
        $this->data = array();

        try {
            
            $result = $this->database
                ->table('roles')
                ->table('users_to_roles')
                ->keys('*')
                ->where('*_DBPREFIX_*roles.id', '=', '*_DBPREFIX_*users_to_roles.role')
                ->andWhere('*_DBPREFIX_*users_to_roles.user', '=', $id)
                ->orderBy('name')
                ->get();
                
        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->loadList($result, 'name');
        
        if ( isset( $this->packages ) ) {
            
            $this->loadPackages($result, 'name');
            
        }
        
        return $result;
        
    }

}
