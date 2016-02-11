<?php namespace Comodojo;

use \Comodojo\Base\Firestarter;
use \Comodojo\Settings\Settings;
use \Comodojo\Extender\Econtrol;
use \Comodojo\Extender\Extender;
use \Comodojo\Base\CacheHandler;
use \Comodojo\Database\EnhancedDatabase;
use \Comodojo\Extender\Shell\Controller;
use \Comodojo\Extender\TasksTable;
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

class Shell {

    use Firestarter;

    public function __construct( $configuration = array() ) {

        $this->getStaticConfiguration($configuration);

        try {

            @$this->getDatabase();

            $this->getConfiguration();

        } catch (Exception $e) {

            echo "\nConfiguration not available; is comodojo installed?\n";

        }

        $idle = $this->configuration->get('extender-idle-time');

        define('EXTENDER_IDLE_TIME', is_null($idle) ? 1 : $idle);
        define('EXTENDER_DATABASE_MODEL', $this->configuration->get('database-model'));
        define('EXTENDER_DATABASE_HOST', $this->configuration->get('database-host'));
        define('EXTENDER_DATABASE_PORT', $this->configuration->get('database-port'));
        define('EXTENDER_DATABASE_NAME', $this->configuration->get('database-name'));
        define('EXTENDER_DATABASE_USER', $this->configuration->get('database-user'));
        define('EXTENDER_DATABASE_PASS', $this->configuration->get('database-password'));
        define('EXTENDER_DATABASE_PREFIX', $this->configuration->get('database-prefix'));
        define('EXTENDER_DATABASE_TABLE_JOBS', $this->configuration->get('extender-database-jobs'));
        define('EXTENDER_DATABASE_TABLE_WORKLOGS', $this->configuration->get('extender-database-worklogs'));
        define('EXTENDER_CACHE_FOLDER', $this->configuration->get('local-cache'));

    }

    public function createEcontrol() {

        $econtrol = new Econtrol();

        $controller = $econtrol->controller();

        $tasks = $econtrol->tasks();

        $controller->add('system', array(
            "description" => "System actions",
            "class" => "\\Comodojo\\Commands\\System",
            "aliases"=> array("sys"),
            "options" => array(
                "force" => array(
                    "short_name" => "-f",
                    "long_name" => "--force",
                    "action" => "StoreTrue",
                    "description" => "Force installation re-creating database"
                ),
                "clean" => array(
                    "short_name" => "-c",
                    "long_name" => "--clean",
                    "action" => "StoreTrue",
                    "description" => "Drain database tables only"
                )
            ),
            "arguments" => array(
                "action" => array(
                    "choices" => array(),
                    "multiple" => false,
                    "optional" => false,
                    "description" => "Action to perform",
                    "help_name" => "status, check, install, pause, resume"
                )
            ))
        );

        if ( $this->database instanceof EnhancedDatabase ) {

            $this->pushCommands($controller);

            $this->pushTasks($tasks);

        }

        return $econtrol;

    }

    private function pushCommands(Controller $controller) {

    }

    private function pushTasks(TasksTable $tasks) {

    }

    private function getConfiguration() {

        $settings = new Settings( $this->database() );

        foreach ( $settings as $setting => $value ) {

            $this->configuration->set($setting, $value);

        }

    }

}
