<?php namespace Comodojo\Commands;

use \Comodojo\Extender\Command\AbstractCommand;
use \Comodojo\Database\Database;
use \Comodojo\Database\EnhancedDatabase;
use \Comodojo\Exception\ShellException;
use \Comodojo\Exception\DatabaseException;
use \Exception;

/**
 * @package     Comodojo extender commands
 * @author      Marco Giovinazzi <marco.giovinazzi@comodojo.org>
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

class System extends AbstractCommand {

    public function execute() {

        $force = $this->getOption("force");

        $clean = $this->getOption("clean");

        $action = $this->getArgument("action");

        try {

            switch ($action) {

                case 'status':

                    $return = $this->status();

                    break;

                case 'check':

                    $return = $this->check();

                    break;

                case 'install':

                    $return = $this->install($force, $clean);

                    break;

                case 'pause':

                    $return = $this->pause();

                    break;

                case 'resume':

                    $return = $this->resume();

                    break;

                default:

                    $return = $this->color->convert("\n%yInvalid action ".$action."%n");

                    break;

            }

        } catch (ShellException $se) {

            throw $se;

        } catch (Exception $e) {

            throw $e;

        }

        return $return;

    }

    private function check() {

        $checks = self::doCheck();

        $constants = $checks['constants'];

        $multithread = $checks['multithread'];

        $signals = $checks['signals'];

        $database = $checks['database'];

        $real_path = $checks['real_path'];

        $multithread_enabled = $checks['multithread_enabled'];

        $idle_time = $checks['idle_time'];

        $max_bytes = $checks['max_result_bytes'];

        $max_childs = $checks['max_childs'];

        $max_childs_runtime = $checks['max_childs_runtime'];

        $parent_niceness = $checks['parent_niceness'];

        $child_niceness = $checks['child_niceness'];

        $return = "Extender checks:\n----------------\n\n";

        $return .= "Extender minimum parameters configured: " . $this->color->convert( $constants === true ? "%gPASSED%n" : "%r".$constants."%n" );

        $return .= "\nMultiprocess support available: " . $this->color->convert( $multithread === true ? "%gYES%n" : "%rNO%n" );

        $return .= "\nDaemon support (signaling): " . $this->color->convert( $signals === true ? "%gYES%n" : "%rNO%n" );

        $return .= "\nExtender database available and configured: " . $this->color->convert( $database === true ? "%gYES%n" : "%rNO%n" );

        $return .= "\n\nExtender parameters:\n--------------------\n\n";

        $return .= "Framework path: " . $this->color->convert( "%g".$real_path."%n");

        $return .= "\nMultiprocess enabled: " . $this->color->convert( "%g".$multithread_enabled."%n");

        $return .= "\nIdle time (daemon mode): " . $this->color->convert( "%g".$idle_time."%n");

        $return .= "\nMax result bytes per task: " . $this->color->convert( "%g".$max_bytes."%n");

        $return .= "\nMax childs: " . $this->color->convert( "%g".$max_childs."%n");

        $return .= "\nMax child runtime: " . $this->color->convert( "%g".$max_childs_runtime."%n");

        $return .= "\nParent niceness: " . $this->color->convert( !is_null($parent_niceness) ? "%g".$parent_niceness."%n" : "%ydefault%n" );

        $return .= "\nChilds niceness: " . $this->color->convert( !is_null($child_niceness) ? "%g".$child_niceness."%n" : "%ydefault%n" );

        return $return;

    }

    private function status() {

        list($pid, $status, $queue) = self::getStatus();

        $return = "\n *** Extender Status Resume *** \n";
        $return .= "  ------------------------------ \n\n";

        $return .= " - Process PID: ".$this->color->convert("%g".$pid."%n")."\n";
        $return .= " - Process running since: ".$this->color->convert("%g".date("r", (int)$status["STARTED"])."%n")."\n";
        $return .= " - Process runtime (sec): ".$this->color->convert("%g".(int)$status["TIME"]."%n")."\n\n";

        $return .= " - Running jobs: ".$this->color->convert("%g".$queue["RUNNING"]."%n")."\n";
        $return .= " - Queued jobs: ".$this->color->convert("%g".$queue["QUEUED"]."%n")."\n\n";

        $return .= " - Current Status: ". ( $status["RUNNING"] == 1 ? $this->color->convert("%gRUNNING%n") : $this->color->convert("%yPAUSED%n") )."\n";
        $return .= " - Completed jobs: ".$this->color->convert("%g".$status["COMPLETED"]."%n")."\n";
        $return .= " - Failed jobs: ".$this->color->convert("%r".$status["FAILED"]."%n")."\n\n";

        $return .= " - Current CPU load (avg): ".$this->color->convert("%g".implode(", ", $status["CPUAVG"])."%n")."\n";
        $return .= " - Allocated memory (real): ".$this->color->convert("%g".self::convert($status["MEM"])."%n")."\n";
        $return .= " - Allocated memory (peak): ".$this->color->convert("%g".self::convert($status["MEMPEAK"])."%n")."\n\n";

        $return .= " - User: ".$this->color->convert("%g".$status["USER"]."%n")."\n";
        $return .= " - Niceness: ".$this->color->convert("%g".$status["NICENESS"]."%n")."\n\n";

        return $return;

    }

    private function install($force, $clean) {

        $this->logger->debug("Checking database status");

        $installed = self::checkInstalled();

        if ( $installed AND $clean ) {

            try {

                $this->logger->info("Truncating database");

                self::emptyDatabase();

            } catch (Exception $se) {

                throw $se;

            }

            return $this->color->convert("\n%gComodojo database cleaned.%n\n");

        }

        if ( $installed AND is_null($force) ) return $this->color->convert("\n%yComodojo already installed, use --force to reinstall.%n\n");

        try {

            $this->logger->info("Installing database");

            self::installDatabase();

        } catch (Exception $e) {

            throw $e;

        }

        return $this->color->convert("\n%gComodojo successfully installed%n\n");

    }

    private static function convert($size) {

        $unit = array('b','kb','mb','gb','tb','pb');

        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];

    }

    private static function doCheck() {

        return array(
            "constants" => Checks::constants(),
            "multithread" => Checks::multithread(),
            "signals" => Checks::signals(),
            "database" => Checks::database(),
            "real_path" => EXTENDER_REAL_PATH,
            "multithread_enabled" => EXTENDER_MULTITHREAD_ENABLED,
            "idle_time" => EXTENDER_IDLE_TIME,
            "max_result_bytes" => EXTENDER_MAX_RESULT_BYTES,
            "max_childs" => EXTENDER_MAX_CHILDS,
            "max_childs_runtime" => EXTENDER_MAX_CHILDS_RUNTIME,
            "parent_niceness" => defined('EXTENDER_PARENT_NICENESS') ? EXTENDER_PARENT_NICENESS : null,
            "child_niceness" => defined('EXTENDER_CHILD_NICENESS') ? EXTENDER_CHILD_NICENESS : null
        );

    }

    private static function getStatus() {

        $lockfile = EXTENDER_CACHE_FOLDER.self::$lockfile;

        $statusfile = EXTENDER_CACHE_FOLDER.self::$statusfile;

        $queuefile = EXTENDER_CACHE_FOLDER.self::$queuefile;

        try {

            $return = self::readStatus( $lockfile, $statusfile, $queuefile );

        } catch (Exception $e) {

            throw new Exception("Unable to read status file (maybe extender stopped?)");

        }

        return $return;

    }

    private static function readStatus($lockfile, $statusfile, $queuefile) {

        set_error_handler(

            function($severity, $message, $file, $line) {

                throw new Exception($message);

            }

        );

        try {

            $lock = file_get_contents($lockfile);

            $status = file_get_contents($statusfile);

            $queue = file_get_contents($queuefile);

        } catch (Exception $se) {

            throw $se;

        }

        restore_error_handler();

        if ( $lock === false ) throw new Exception("Unable to read lock file");

        if ( $status === false ) throw new Exception("Unable to read status file");

        if ( $queue === false ) throw new Exception("Unable to read queue file");

        return array(
            $lock,
            unserialize($status),
            unserialize($queue)
        );

    }

    private static function installDatabase() {

        $queries = self::getInstallQueries();

        try {

            $db = new Database(
                EXTENDER_DATABASE_MODEL,
                EXTENDER_DATABASE_HOST,
                EXTENDER_DATABASE_PORT,
                EXTENDER_DATABASE_NAME,
                EXTENDER_DATABASE_USER,
                EXTENDER_DATABASE_PASS
            );

            foreach ($queries as $query) {

                $db->query($query);

            }

        } catch (DatabaseException $de) {

            unset($db);

            throw new ShellException("Database error: ".$de->getMessage());

        }

        unset($db);

    }

    private static function emptyDatabase() {

        try {

            $db = new EnhancedDatabase(
                EXTENDER_DATABASE_MODEL,
                EXTENDER_DATABASE_HOST,
                EXTENDER_DATABASE_PORT,
                EXTENDER_DATABASE_NAME,
                EXTENDER_DATABASE_USER,
                EXTENDER_DATABASE_PASS
            );

            $db->autoClean();

            foreach ( array_reverse(array_keys(self::getInstallQueries())) as $table ) {

                $db->tablePrefix(EXTENDER_DATABASE_PREFIX)->table($table)->truncate();
            }

        } catch (DatabaseException $de) {

            unset($db);

            throw new ShellException("Database error: ".$de->getMessage());

        }

        unset($db);

    }

    private static function checkInstalled() {

        try {

            $db = new EnhancedDatabase(
                EXTENDER_DATABASE_MODEL,
                EXTENDER_DATABASE_HOST,
                EXTENDER_DATABASE_PORT,
                EXTENDER_DATABASE_NAME,
                EXTENDER_DATABASE_USER,
                EXTENDER_DATABASE_PASS
            );

            $db->tablePrefix(EXTENDER_DATABASE_PREFIX)->table("settings")->keys("id")->get(1);

        } catch (DatabaseException $de) {

            unset($db);

            return false;

        }

        unset($db);

        return true;

    }

    private static function getInstallQueries() {

        return array(
            "settings" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."settings` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(256) NOT NULL,
              `value` TEXT NULL DEFAULT NULL,
              `constant` TINYINT(1) NULL DEFAULT 0,
              `type` VARCHAR(16) NOT NULL DEFAULT 'STRING',
              `validation` TEXT NULL DEFAULT NULL,
              `package` VARCHAR(256) NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `name_UNIQUE` (`name` ASC))
            ENGINE = InnoDB;",
            "applications" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."applications` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(256) NOT NULL,
              `description` TEXT NULL DEFAULT NULL,
              `package` VARCHAR(256) NOT NULL,
              PRIMARY KEY (`id`))
            ENGINE = InnoDB;",
            "roles" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."roles` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(128) NOT NULL,
              `description` TEXT NULL DEFAULT NULL,
              `landingapp` INT UNSIGNED NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `name_UNIQUE` (`name` ASC),
              INDEX `landingapp_idx` (`landingapp` ASC),
              CONSTRAINT `landingapp`
                FOREIGN KEY (`landingapp`)
                REFERENCES `".EXTENDER_DATABASE_PREFIX."applications` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;",
            "authentication" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."authentication` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(256) NOT NULL,
              `description` TEXT NULL DEFAULT NULL,
              `class` VARCHAR(256) NOT NULL,
              `parameters` TEXT NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `name_UNIQUE` (`name` ASC))
            ENGINE = InnoDB;",
            "users" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."users` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `username` VARCHAR(128) NOT NULL,
              `password` VARCHAR(128) NOT NULL,
              `displayname` VARCHAR(256) NOT NULL,
              `mail` VARCHAR(256) NOT NULL,
              `birthdate` DATE NULL DEFAULT NULL,
              `gender` VARCHAR(1) NULL DEFAULT NULL,
              `enabled` TINYINT(1) NULL DEFAULT 0,
              `authentication` INT UNSIGNED NULL DEFAULT NULL,
              `primaryrole` INT UNSIGNED NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `username_UNIQUE` (`username` ASC),
              UNIQUE INDEX `mail_UNIQUE` (`mail` ASC),
              INDEX `authentication_idx` (`authentication` ASC),
              INDEX `primaryrole_idx` (`primaryrole` ASC),
              CONSTRAINT `authentication`
                FOREIGN KEY (`authentication`)
                REFERENCES `".EXTENDER_DATABASE_PREFIX."authentication` (`id`)
                ON DELETE SET NULL
                ON UPDATE CASCADE,
              CONSTRAINT `primaryrole`
                FOREIGN KEY (`primaryrole`)
                REFERENCES `".EXTENDER_DATABASE_PREFIX."roles` (`id`)
                ON DELETE SET NULL
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;",
            "users_to_roles" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."users_to_roles` (
              `user` INT UNSIGNED NOT NULL,
              `role` INT UNSIGNED NOT NULL,
              UNIQUE INDEX `userrole` (`user` ASC, `role` ASC),
              INDEX `role_idx` (`role` ASC),
              CONSTRAINT `utr_user`
                FOREIGN KEY (`user`)
                REFERENCES `".EXTENDER_DATABASE_PREFIX."users` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `utr_role`
                FOREIGN KEY (`role`)
                REFERENCES `".EXTENDER_DATABASE_PREFIX."roles` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB;",
            "applications_to_roles" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."applications_to_roles` (
              `application` INT UNSIGNED NOT NULL,
              `role` INT UNSIGNED NOT NULL,
              UNIQUE INDEX `approle` (`application` ASC, `role` ASC),
              INDEX `role_idx` (`role` ASC),
              CONSTRAINT `atr_application`
                FOREIGN KEY (`application`)
                REFERENCES `".EXTENDER_DATABASE_PREFIX."applications` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `atr_role`
                FOREIGN KEY (`role`)
                REFERENCES `".EXTENDER_DATABASE_PREFIX."roles` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
            ENGINE = InnoDB;",
            "routes" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."routes` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `route` VARCHAR(256) NOT NULL,
              `type` VARCHAR(16) NOT NULL DEFAULT 'ROUTE',
              `class` VARCHAR(256) NOT NULL,
              `parameters` TEXT NULL,
              `package` VARCHAR(256) NOT NULL,
              `application` INT UNSIGNED NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `route_UNIQUE` (`route` ASC),
              INDEX `application` (`application` ASC),
              CONSTRAINT `application`
                FOREIGN KEY (`application`)
                REFERENCES `".EXTENDER_DATABASE_PREFIX."applications` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;",
            "plugins" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."plugins` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(256) NOT NULL,
              `class` VARCHAR(256) NOT NULL,
              `method` VARCHAR(256) NULL DEFAULT NULL,
              `event` VARCHAR(256) NOT NULL,
              `framework` VARCHAR(16) NOT NULL,
              `package` VARCHAR(256) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `plugin` (`name` ASC, `framework` ASC))
            ENGINE = InnoDB;",
            "themes" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."themes` (
              `id` INT NOT NULL,
              `name` VARCHAR(256) NOT NULL,
              `description` TEXT NULL DEFAULT NULL,
              `package` VARCHAR(256) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `name_UNIQUE` (`name` ASC))
            ENGINE = InnoDB;",
            "commands" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."commands` (
              `id` INT NOT NULL,
              `command` VARCHAR(256) NOT NULL,
              `class` VARCHAR(256) NOT NULL,
              `description` TEXT NULL DEFAULT NULL,
              `aliases` TEXT NOT NULL,
              `options` TEXT NOT NULL,
              `arguments` TEXT NOT NULL,
              `package` VARCHAR(256) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `command_UNIQUE` (`command` ASC))
            ENGINE = InnoDB;",
            "tasks" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."tasks` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(256) NOT NULL,
              `class` VARCHAR(256) NOT NULL,
              `description` TEXT NULL DEFAULT NULL,
              `package` VARCHAR(256) NULL DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `name_UNIQUE` (`name` ASC))
            ENGINE = InnoDB;",
            "rpc" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."rpc` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(256) NOT NULL,
              `callback` VARCHAR(256) NOT NULL,
              `method` VARCHAR(256) NULL DEFAULT NULL,
              `description` TEXT NULL DEFAULT NULL,
              `signatures` TEXT NOT NULL,
              `package` VARCHAR(256) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `name_UNIQUE` (`name` ASC))
            ENGINE = InnoDB;",
            "jobs" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."jobs` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(128) NOT NULL,
              `task` VARCHAR(128) NOT NULL,
              `description` TEXT NULL DEFAULT NULL,
              `enabled` TINYINT(1) NULL DEFAULT 0,
              `min` VARCHAR(16) DEFAULT NULL,
              `hour` VARCHAR(16) DEFAULT NULL,
              `dayofmonth` VARCHAR(16) DEFAULT NULL,
              `month` VARCHAR(16) DEFAULT NULL,
              `dayofweek` VARCHAR(16) DEFAULT NULL,
              `year` VARCHAR(16) DEFAULT NULL,
              `params` TEXT NULL DEFAULT NULL,
              `lastrun` INT(64) DEFAULT NULL,
              `firstrun` INT(64) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE INDEX `name_UNIQUE` (`name` ASC))
            ENGINE = InnoDB;",
            "worklogs" => "CREATE TABLE IF NOT EXISTS `".EXTENDER_DATABASE_PREFIX."worklogs` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `pid` INT UNSIGNED DEFAULT NULL,
              `jobid` INT UNSIGNED DEFAULT NULL,
              `name` VARCHAR(128) NOT NULL,
              `task` VARCHAR(128) NOT NULL,
              `status` VARCHAR(12) NOT NULL,
              `success` TINYINT(1) NULL DEFAULT 0,
              `result` TEXT NULL DEFAULT NULL,
              `start` VARCHAR(128) NOT NULL,
              `end` VARCHAR(128) DEFAULT NULL,
              PRIMARY KEY (`id`))
            ENGINE = InnoDB;"
        );

    }


}
