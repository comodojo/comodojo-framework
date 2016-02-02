<?php namespace Comodojo\Configuration;

use \Comodojo\Plugins\Plugin as FrameworkPlugin;
use \Comodojo\Plugins\Plugins as FrameworkPlugins;
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

class Plugins extends AbstractConfiguration {

    public function get() {

        $return = new FrameworkPlugins($this->database());

        return $return;

    }

    public function getByFramework($framework) {

        $return = array();

        $plugins = $this->get();

        $list = $plugins->getListByFramework($framework);

        if (!empty($list)) {

            foreach ($list as $name) {

                array_push($return, $plugins->getByName($name));

            }

        }

        return $return;

    }
    
    protected function parameters() {
        
        return array(
            "package" => null,
            "framework" => null,
            "name" => null,
            "class" => null,
            "method" => "",
            "event" => "" 
        );
        
        
    }

    protected function save($params) {
        
        if ($params['id'] == 0)
            $return = new FrameworkPlugin($this->database());
        else
            $return = $this->getById($id);
            
        if (empty($return)) throw new ConfigurationException("Unable to load object");
            
        $return->setName($params['name'])
            ->setPackage($params['package'])
            ->setFramework($params['framework'])
            ->setClass($params['class'])
            ->setMethod($params['method'])
            ->setEvent($params['event'])
            ->save();

        return $return;

    }

}
