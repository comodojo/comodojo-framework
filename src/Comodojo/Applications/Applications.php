<?php namespace Comodojo\Applications;

use \Comodojo\Base\Iterator;
use \Comodojo\Base\PackagesTrait;
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

class Applications extends Iterator {

    use PackagesTrait;

	public function getByID($id) {

		return Application::load($this->database, intval($id));

	}
	
	public function loadByRole($role_or_id) {
	    
	    if ( $role_or_id instanceof Role ) {
	        
	        $id = $role_or_id->getId();
	        
	    } else if ( is_int($role_or_id) ) {
	        
	        $id = $role_or_id;
	        
	    } else {
	        
	        throw new Exception("Invalid role object or id");
	        
	    }
	    
	    try {
	        
	        $return = $this->loadDataByRole($id);    
	        
	    } catch (Exception $e) {
	        
	        throw $e;
	        
	    }
	    
	    return $return;
	    
	}

	protected function loadData() {

        $this->loadFromDatabase("applications", "name");

	}
	
	protected function loadDataByRole($id) {
        
        // reset data object
        $this->data = array();

        try {
            
            $result = $this->database
                ->table('applications')
                ->table('applications_to_roles')
                ->keys('*')
                ->where('*_DBPREFIX_*applications.id', '=', '*_DBPREFIX_*applications_to_roles.application')
                ->andWhere('*_DBPREFIX_*applicationss_to_roles.role', '=', $id)
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
