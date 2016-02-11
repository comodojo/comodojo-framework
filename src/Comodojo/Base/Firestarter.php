<?php namespace Comodojo\Base;

use \Comodojo\Settings\Settings;
use \Comodojo\Database\Database;
use \Comodojo\Database\EnhancedDatabase;
use \Comodojo\Dispatcher\Components\Configuration;
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

    protected $database;

    protected $configuration;

    public function database() {

        return $this->database;

    }

    public function configuration() {

        return $this->configuration;

    }

    protected function getDatabase( ) {

        $model = $this->configuration->get('database-model');
        $host = $this->configuration->get('database-host');
        $port = $this->configuration->get('database-port');
        $name = $this->configuration->get('database-name');
        $user = $this->configuration->get('database-user');
        $password = $this->configuration->get('database-password');
        $prefix = $this->configuration->get('database-prefix');

        try {

            $this->database = new EnhancedDatabase(
                $model,
                $host,
                $port,
                $name,
                $user,
                $password
            );

            $this->database->tablePrefix($prefix);

            $this->database->autoClean();

        } catch (DatabaseException $de) {

            throw $de;

        } catch (Exception $e) {

            throw $e;

        }

    }

    protected function getStaticConfiguration( $static_configuration = array() ) {

        $this->configuration = new Configuration();

        foreach ( $static_configuration as $setting => $value ) {

            $this->configuration->set($setting, $value);

        }

    }

}
