<?php namespace Comodojo\Configuration;

use \Comodojo\Authentication\Manager as AuthenticationManager;
use \Comodojo\Authentication\Authentication;
use \Comodojo\Database\Database;
use \Comodojo\Exception\DatabaseException;
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

class Configurator {
	
	private $dbh      = null;
	
	function __construct() {
		
		$this->loadDatabase();
		
	}
	
	public function getApplications() {
		
		$return = new Apps($this->dbh);
		
		return $return;
		
	}
	
	public function getApplicationByName($name) {
		
		$return = new Apps($this->dbh);
		
		return $return->getElementByName($name);
		
	}
	
	public function getApplicationByID($id) {
		
		return App::load($id, $this->dbh);
		
	}
	
	public function addApplication($package, $name, $description = "") {
		
		$return = new App($this->dbh);
		
		$return->setName($name)
			->setPackage($package)
			->setDescription($description)
			->save();
		
		return $return;
		
	}
	
	public function updateApplication($id, $package, $name, $description = "") {
		
		$return = App::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		$return->setName($name)
			->setPackage($package)
			->setDescription($description)
			->save();
		
		return $return;
		
	}
	
	public function deleteApplication($id) {
		
		$return = App::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		return $return->delete();
		
	}
	
	public function getAuthenticationManager() {
		
		$return = new AuthenticationManager($this->dbh);
		
		return $return;
		
	}
	
	public function getApplicationByName($name) {
		
		$return = new AuthenticationManager($this->dbh);
		
		return $return->getElementByName($name);
		
	}
	
	public function getApplicationByID($id) {
		
		return Authentication::load($id, $this->dbh);
		
	}
	
	public function addAuthentication($package, $name, $class, $description = "") {
		
		$return = new Authentication($this->dbh);
		
		$return->setName($name)
			->setPackage($package)
			->setClass($class)
			->setDescription($description)
			->save();
		
		return $return;
		
	}
	
	public function updateAuthentication($id, $package, $name, $class, $description = "") {
		
		$return = Authentication::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		$return->setName($name)
			->setPackage($package)
			->setClass($class)
			->setDescription($description)
			->save();
		
		return $return;
		
	}
	
	public function deleteAuthentication($id) {
		
		$return = Authentication::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		return $return->delete();
		
	}
	
	public function getCommands() {
		
		$return = new Commands($this->dbh);
		
		return $return;
		
	}
	
	public function getCommandByName($name) {
		
		$return = new Commands($this->dbh);
		
		return $return->getElementByName($name);
		
	}
	
	public function getCommandByID($id) {
		
		return Command::load($id, $this->dbh);
		
	}
	
	public function addCommand($package, $name, $class, $description = "", $aliases = array(), $options = array(), $arguments = array()) {
		
		$return = new Command($this->dbh);
		
		$return->setName($name)
			->setPackage($package)
			->setClass($class)
			->setDescription($description)
			->setAliases($aliases)
			->setRawOptions($options)
			->setRawArguments($arguments)
			->save();
		
		return $return;
		
	}
	
	public function updateCommand($id, $package, $name, $class, $description = "", $aliases = array(), $options = array(), $arguments = array()) {
		
		$return = Command::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		$return->setName($name)
			->setPackage($package)
			->setClass($class)
			->setDescription($description)
			->setAliases($aliases)
			->setRawOptions($options)
			->setRawArguments($arguments)
			->save();
		
		return $return;
		
	}
	
	public function deleteCommand($id) {
		
		$return = Command::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		return $return->delete();
		
	}
	
	public function getPlugins() {
		
		$return = new Plugins($this->dbh);
		
		return $return;
		
	}
	
	public function getPluginsByFramework($framework) {
		
		$return = array();
		
		$plugins = new Plugins($this->dbh);
		
		$list = $plugins->getListByFramework($framework);
		
		if (!empty($list)) {
			
			foreach ($list as $name)
				array_push($return, $plugins->getElementByName($name));
			
		}
		
		return $return;
		
	}
	
	public function getPluginByName($name) {
		
		$return = new Plugins($this->dbh);
		
		return $return->getElementByName($name);
		
	}
	
	public function getPluginByID($id) {
		
		return Plugin::load($id, $this->dbh);
		
	}
	
	public function addPlugin($package, $framework, $name, $class, $method = "", $event = "") {
		
		$return = new Plugin($this->dbh);
		
		$return->setName($name)
			->setPackage($package)
			->setFramework($framework)
			->setClass($class)
			->setMethod($method)
			->setEvent($event)
			->save();
		
		return $return;
		
	}
	
	public function updatePlugin($id, $package, $framework, $name, $class, $method = "", $event = "") {
		
		$return = Plugin::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		$return->setName($name)
			->setPackage($package)
			->setFramework($framework)
			->setClass($class)
			->setMethod($method)
			->setEvent($event)
			->save();
		
		return $return;
		
	}
	
	public function deletePlugin($id) {
		
		$return = Plugin::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		return $return;
		
	}
	
	public function getRoutes() {
		
		$return = new Routes($this->dbh);
		
		return $return;
		
	}
	
	public function getRouteByName($name) {
		
		$return = new Routes($this->dbh);
		
		return $return->getElementByName($name);
		
	}
	
	public function getRouteByID($id) {
		
		return Route::load($id, $this->dbh);
		
	}
	
	public function addRoute($package, $name, $class, $type, $paramseters = array()) {
		
		$return = new Route($this->dbh);
		
		$return->setName($name)
			->setPackage($package)
			->setClass($class)
			->setType($type)
			->save();
			
		foreach ($paramseters as $key => $value)
			$return->setParameter($key, $value);
		
		return $return->save();
		
	}
	
	public function updateRoute($id, $package, $name, $class, $type, $paramseters = array()) {
		
		$return = Route::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		$return->setName($name)
			->setPackage($package)
			->setClass($class)
			->setType($type)
			->save();
			
		foreach ($return->getParameters() as $param)
			$return->unsetParameter($param);
			
		foreach ($paramseters as $key => $value)
			$return->setParameter($key, $value);
		
		return $return->save();
		
	}
	
	public function deleteRoute($id) {
		
		$return = Route::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		return $return;
		
	}
	
	public function getRpcMethods() {
		
		$return = new Rpc($this->dbh);
		
		return $return;
		
	}
	
	public function getRpcMethodByName($name) {
		
		$return = new Rpc($this->dbh);
		
		return $return->getElementByName($name);
		
	}
	
	public function getRpcMethodByID($id) {
		
		return RpcMethod::load($id, $this->dbh);
		
	}
	
	public function addRpcMethod($package, $name, $callback, $method, $description = "", $signatures = array()) {
		
		$return = new RpcMethod($this->dbh);
		
		$return->setName($name)
			->setPackage($package)
			->setCallback($class)
			->setMethod($method)
			->setDescription($description)
			->setRawSignatures($signatures)
			->save();
		
		return $return;
		
	}
	
	public function updateRpcMethod($id, $package, $name, $callback, $method, $description = "", $signatures = array()) {
		
		$return = RpcMethod::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		$return->setName($name)
			->setPackage($package)
			->setCallback($class)
			->setMethod($method)
			->setDescription($description)
			->setRawSignatures($signatures)
			->save();
		
		return $return;
		
	}
	
	public function deleteRpcMethod($id) {
		
		$return = RpcMethod::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		return $return;
		
	}
	
	public function getSettings() {
		
		$return = new Settings($this->dbh);
		
		return $return;
		
	}
	
	public function getSettingsByPackage($package) {
		
		$return = array();
		
		$settings = new Settings($this->dbh);
		
		$list = $settings->getListByPackage($package);
		
		if (!empty($list)) {
			
			foreach ($list as $name)
				array_push($return, $settings->getElementByName($name));
			
		}
		
		$return = new Settings($this->dbh);
		
		return $return;
		
	}
	
	public function getSettingByName($name) {
		
		$return = new Settings($this->dbh);
		
		return $return->getElementByName($name);
		
	}
	
	public function getSettingByID($id) {
		
		return Setting::load($id, $this->dbh);
		
	}
	
	public function addSetting($package, $name, $value) {
		
		$return = new Setting($this->dbh);
		
		$return->setName($name)
			->setPackage($package)
			->setValue($value)
			->save();
		
		return $return;
		
	}
	
	public function updateSetting($id, $package, $name, $value) {
		
		$return = Setting::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		$return->setName($name)
			->setPackage($package)
			->setValue($value)
			->save();
		
		return $return;
		
	}
	
	public function deleteSetting($id) {
		
		$return = Setting::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		return $return;
		
	}
	
	public function getTasks() {
		
		$return = new Tasks($this->dbh);
		
		return $return;
		
	}
	
	public function getTaskByName($name) {
		
		$return = new Tasks($this->dbh);
		
		return $return->getElementByName($name);
		
	}
	
	public function getTaskByID($id) {
		
		return Task::load($id, $this->dbh);
		
	}
	
	public function addTask($package, $name, $class, $description = "") {
		
		$return = new Task($this->dbh);
		
		$return->setName($name)
			->setPackage($package)
			->setClass($class)
			->setDescription($description)
			->save();
		
		return $return;
		
	}
	
	public function updateTask($id, $package, $name, $class, $description = "") {
		
		$return = Task::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		$return->setName($name)
			->setPackage($package)
			->setClass($class)
			->setDescription($description)
			->save();
		
		return $return;
		
	}
	
	public function deleteTask($id) {
		
		$return = Task::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		return $return->delete();
		
	}
	
	public function getThemes() {
		
		$return = new Themes($this->dbh);
		
		return $return;
		
	}
	
	public function getThemeByName($name) {
		
		$return = new Themes($this->dbh);
		
		return $return->getElementByName($name);
		
	}
	
	public function getThemeByID($id) {
		
		return Theme::load($id, $this->dbh);
		
	}
	
	public function addTheme($package, $name, $description = "") {
		
		$return = new Theme($this->dbh);
		
		$return->setName($name)
			->setPackage($package)
			->setDescription($description)
			->save();
		
		return $return;
		
	}
	
	public function updateTheme($id, $package, $name, $description = "") {
		
		$return = Theme::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		$return->setName($name)
			->setPackage($package)
			->setDescription($description)
			->save();
		
		return $return;
		
	}
	
	public function deleteTheme($id) {
		
		$return = Theme::load($id, $this->dbh);
		
		if (empty($return)) throw new ConfigurationException("The specified ID doesn't exist");
		
		return $return->delete();
		
	}
	
	private function loadDatabase() {
		
		// TO DO
		
	}

}
