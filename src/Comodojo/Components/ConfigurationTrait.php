<?php namespace Comodojo\Components;

use \Comodojo\Dispatcher\Components\Configuration;
use \Exception;

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

trait ConfigurationTrait {

    protected $configuration;

    public function configuration() {

        return $this->configuration;

    }

    protected function initConfiguration( $static_configuration = array() ) {

        $this->configuration = new Configuration();

        foreach ( $static_configuration as $setting => $value ) {

            $this->configuration->set($setting, $value);

        }

        return $this->configuration;

    }

}
