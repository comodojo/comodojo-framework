<?php namespace Comodojo\Users;

use \Comodojo\Base\Iterator;
use \Comodojo\Roles\Role;

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

class Users extends Iterator {

	public function getByID($id) {

		return User::load($this->database, intval($id));

	}

    protected function loadData() {

        $this->loadFromDatabase("users", "username");

    }
    
    public function loadByRole($role_or_id) {
	    
	    if ( $role_or_id instanceof Role ) {
	        
	        $id = $role_or_id->getID();
	        
	    } else if ( is_int($role_or_id) ) {
	        
	        $id = $role_or_id;
	        
	    } else {
	        
	        throw new Exception("Invalid user object or id");
	        
	    }
	    
	    try {
	        
	        $return = $this->loadDataByRole($id);    
	        
	    } catch (Exception $e) {
	        
	        throw $e;
	        
	    }
	    
	    return $return;
	    
	}
	
	protected function loadDataByRole($id) {
        
        // reset data object
        $this->data = array();

        try {
            
            $result = $this->database
                ->table('users')
                ->table('users_to_roles')
                ->keys('*')
                ->where('*_DBPREFIX_*users.id', '=', '*_DBPREFIX_*users_to_roles.user')
                ->andWhere('*_DBPREFIX_*users_to_roles.role', '=', $id)
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
