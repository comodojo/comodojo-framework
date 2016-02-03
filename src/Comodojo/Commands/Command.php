<?php namespace Comodojo\Commands;

use \Comodojo\Database\EnhancedDatabase;
use \Comodojo\Base\Element;
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

class Command extends Element {

    protected $classname = "";

    protected $description = "";

    protected $aliases = array();

    protected $options = array();

    protected $arguments = array();

    public function getAliases() {

        return $this->aliases;

    }

    public function setAliases($aliases) {

        $this->aliases = $aliases;

        return $this;

    }

    public function addAlias($alias) {

        if (!in_array($alias, $this->aliases)) {

            array_push($this->aliases, $alias);

            sort($this->aliases);

        }

        return $this;

    }

    public function removeAlias($alias) {

        if (in_array($alias, $this->aliases)) {

            $idx = array_search($alias, $this->aliases);

            array_splice($this->aliases, $idx, 1);

        }

        return $this;

    }

    public function getOptions() {

        return array_keys($this->options);

    }

    public function getRawOptions() {

        return $this->options;

    }

    public function setRawOptions($options) {

        $this->options = array();

        foreach ($options as $name => $option) {

            $this->addOption($name, $option['short_name'], $option['long_name'], $option['action'], $option['description']);

        }

        return $this;

    }

    public function addOption($name, $short = "", $long = "", $action = "StoreTrue", $description = "") {

        if ( empty($short) ) $short = "-"  . substr($name, 0, 1);
        if ( empty($long) )  $long  = "--" . $name;

        if ( !isset($this->options[$name]) ) {

            $this->options[$name] = array(
                'short_name'  => $short,
                'long_name'   => $long,
                'action'      => $action,
                'description' => $description
            );

        }

        return $this;

    }

    public function removeOption($name) {

        if (isset($this->options[$name])) {

            unset($this->options[$name]);

        }

        return $this;

    }

    public function hasOption($name) {

        return isset($this->options[$name]);

    }

    public function getOptionShortName($name) {

        if (isset($this->options[$name])) {

            return $this->options[$name]['short_name'];

        }

        return null;

    }

    public function setOptionShortName($optName, $value) {

        if (isset($this->options[$optName])) {

            $this->options[$optName]['short_name'] = $value;

        }

        return $this;

    }

    public function getOptionLongName($optName) {

        if (isset($this->options[$optName])) {

            return $this->options[$optName]['long_name'];

        }

        return null;

    }

    public function setOptionLongName($optName, $value) {

        if (isset($this->options[$optName])) {

            $this->options[$optName]['long_name'] = $value;

        }

        return $this;

    }

    public function getOptionAction($optName) {

        if (isset($this->options[$optName])) {

            return $this->options[$optName]['action'];

        }

        return null;

    }

    public function setOptionAction($optName, $value) {

        if (isset($this->options[$optName])) {

            $this->options[$optName]['action'] = $value;

        }

        return $this;

    }

    public function getOptionDescription($optName) {

        if (isset($this->options[$optName])) {

            return $this->options[$optName]['description'];

        }

        return null;

    }

    public function setOptionDescription($optName, $value) {

        if (isset($this->options[$optName])) {

            $this->options[$optName]['description'] = $value;

        }

        return $this;

    }

    public function getArguments() {

        return array_keys($this->arguments);

    }

    public function getRawArguments() {

        return $this->arguments;

    }

    public function setRawArguments($arguments) {

        $this->arguments = array();

        foreach ($arguments as $name => $arg)
            $this->addArgument($name, $arg['choices'], $arg['multiple'], $arg['optional'], $arg['description']);

        return $this;

    }

    public function addArgument($argName, $choices = array(), $multiple = false, $optional = false, $description = "") {

        if (!isset($this->arguments[$argName])) {

            $this->arguments[$argName] = array(
                'choices'     => $choices,
                'multiple'    => $multiple,
                'optional'    => $optional,
                'description' => $description,
                'help_name'   => implode(", ", $choices)
            );

        }

        return $this;

    }

    public function removeArgument($argName) {

        if (isset($this->arguments[$argName])) {

            unset($this->arguments[$argName]);

        }

        return $this;

    }

    public function hasArgument($argName) {

        return (isset($this->arguments[$argName]));

    }

    public function getArgumentChoices($argName) {

        if (isset($this->arguments[$argName])) {

            return $this->arguments[$argName]['choices'];

        }

        return null;

    }

    public function setArgumentChoices($argName, $value) {

        if (isset($this->arguments[$argName])) {

            $this->arguments[$argName]['choices']   = $value;

            $this->arguments[$argName]['help_name'] = implode(", ", $value);

        }

        return $this;

    }

    public function getArgumentMultipleValues($argName) {

        if (isset($this->arguments[$argName])) {

            return $this->arguments[$argName]['multiple'];

        }

        return null;

    }

    public function setArgumentMultipleValues($argName, $value) {

        if (isset($this->arguments[$argName])) {

            $this->arguments[$argName]['multiple'] = $value;

        }

        return $this;

    }

    public function getArgumentOptional($argName) {

        if (isset($this->arguments[$argName])) {

            return $this->arguments[$argName]['optional'];

        }

        return null;

    }

    public function setArgumentOptional($argName, $value) {

        if (isset($this->arguments[$argName])) {

            $this->arguments[$argName]['optional'] = $value;

        }

        return $this;

    }

    public function getArgumentHelpName($argName) {

        if (isset($this->arguments[$argName])) {

            return $this->arguments[$argName]['help_name'];

        }

        return null;

    }

    public function getArgumentDescription($argName) {

        if (isset($this->arguments[$argName])) {

            return $this->arguments[$argName]['description'];

        }

        return null;

    }

    public function setArgumentDescription($argName, $value) {

        if (isset($this->arguments[$argName])) {

            $this->arguments[$argName]['description'] = $value;

        }

        return $this;

    }

    public function getClass() {

        return $this->classname;

    }

    public function getInstance() {

        $class = $this->classname;

        if (class_exists($class))
            return new $class();

        return null;

    }

    public function setClass($class) {

        $this->classname = $class;

        return $this;

    }

    public function getDescription() {

        return $this->desc;

    }

    public function setDescription($description) {

        $this->desc = $description;

        return $this;

    }

    public static function load($id, $dbh) {

        try {

            $result = Model::load($database, $id);

        } catch (DatabaseException $de) {

            throw $de;

        }

        if ($result->getLength() > 0) {

            $data = $result->getData();

            $data = array_values($data[0]);

            $command = new Command($dbh);

            $command->setData($data);

        } else {

            throw new Exception("Unable to load command");

        }

        return $command;

    }

    protected function getData() {

        return array(
            $this->id,
            $this->name,
            $this->classname,
            $this->description,
            json_encode($this->aliases),
            json_encode($this->options),
            json_encode($this->arguments),
            $this->package
        );

    }

    protected function setData($data) {

        $this->id = intval($data[0]);
        $this->name = $data[1];
        $this->classname = $data[2];
        $this->description = $data[3];
        $this->aliases = json_decode($data[4], true);
        $this->options = json_decode($data[5], true);
        $this->arguments = json_decode($data[6], true);
        $this->package = $data[7];

        return $this;

    }

    protected function create() {

        try {

            $result = Model::create(
                $this->database,
                $this->name,
                $this->classname,
                $this->constant,
                $this->type,
                $this->validation,
                $this->package
            );

        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->id = $result->getInsertId();

        return $this;

    }

    protected function update() {

        try {

            $result = Model::update(
                $this->database,
                $this->id,
                $this->name,
                $this->classname,
                $this->constant,
                $this->type,
                $this->validation,
                $this->package
            );

        } catch (DatabaseException $de) {

            throw $de;

        }

        return $this;

    }

    public function delete() {

        try {

            $result = Model::delete(
                $this->database,
                $this->id
            );

        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->setData(array(0, "", "", "", "[]", "[]", "[]", ""));

        return $this;

    }

}
