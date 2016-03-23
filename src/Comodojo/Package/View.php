<?php namespace Comodojo\Package;

use \Comodojo\Rpc\Iterator as RpcIterator;
use \Comodojo\Plugin\Iterator as PluginIterator;
use \Comodojo\Route\Iterator as RouteIterator;
use \Comodojo\Theme\Iterator as ThemeIterator;
use \Comodojo\Task\Iterator as TaskIterator;
use \Comodojo\Command\Iterator as CommandIterator;
use \Comodojo\Application\Iterator as ApplicationIterator;
use \Comodojo\Authentication\Iterator as AuthenticationIterator;
use \Comodojo\Setting\Iterator as SettingIterator;
use \Comodojo\Components\ViewTrait;
use \Comodojo\Dispatcher\Components\Configuration;
use \Comodojo\Database\EnhancedDatabase;
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

class View extends Model {

    use ViewTrait;

    public function getRpc() {

        $filter = array("package","=",$this->id);

        return RpcIterator::loadBy($filter, $this->configuration(), $this->database());

    }

    public function getPlugins() {

        $filter = array("package","=",$this->id);

        return PluginIterator::loadBy($filter, $this->configuration(), $this->database());

    }

    public function getRoutes() {

        $filter = array("package","=",$this->id);

        return RouteIterator::loadBy($filter, $this->configuration(), $this->database());

    }

    public function getThemes() {

        $filter = array("package","=",$this->id);

        return ThemeIterator::loadBy($filter, $this->configuration(), $this->database());

    }

    public function getTasks() {

        $filter = array("package","=",$this->id);

        return TaskIterator::loadBy($filter, $this->configuration(), $this->database());

    }

    public function getCommands() {

        $filter = array("package","=",$this->id);

        return CommandIterator::loadBy($filter, $this->configuration(), $this->database());

    }

    public function getApplications() {

        $filter = array("package","=",$this->id);

        return ApplicationIterator::loadBy($filter, $this->configuration(), $this->database());

    }

    public function getAuthentications() {

        $filter = array("package","=",$this->id);

        return AuthenticationIterator::loadBy($filter, $this->configuration(), $this->database());

    }

    public function getSettings() {

        $filter = array("package","=",$this->id);

        return SettingIterator::loadBy($filter, $this->configuration(), $this->database());

    }

}
