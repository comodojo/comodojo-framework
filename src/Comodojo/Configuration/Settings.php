<?php namespace Comodojo\Configuration;

use \Comodojo\Settings\Setting as FrameworkSetting;
use \Comodojo\Settings\Settings as FrameworkSettings;
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

class Settings extends AbstractConfiguration {

    public function get() {

        $return = new FrameworkSettings($this->getDbh());

        return $return;

    }
    
    protected function parameters() {
        
        return array(
            "package"     => null,
            "name"        => null,
            "value"       => null,
            "constant"    => false,
            "type"        => "STRING",
            "validate"    => ""
        );
        
        
    }

    protected function save($params) {
        
        if ($params['id'] == 0)
            $return = new FrameworkSetting($this->getDbh());
        else
            $return = $this->getById($id);
            
        if (empty($return)) throw new ConfigurationException("Unable to load object");
            
        $return->setName($params['name'])
            ->setPackage($params['package'])
            ->setConstant($params['constant'])
            ->setType($params['type'])
            ->setValidation($params['validate'])
            ->setValue($params['value'])
            ->save();

        return $return;

    }

}
