<?php namespace Comodojo\Components;

use \Comodojo\Database\EnhancedDatabase;
use \Comodojo\Dispatcher\Components\Configuration;
use \Comodojo\Exception\DatabaseException;
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

abstract class ComodojoModel extends AbstractModel {

    private $args;

    protected $schema

    public function __construct(
        $schema,
        $fields,
        $values = array(),
        EnhancedDatabase $database = null,
        Configuration $configuration = null
    ) {

        $this->args = func_get_args();

        parent::__construct($database, $configuration);

        $this->data['id'] = 0;

        $this->schema = $schema;

        $this->fields = array_merge($this->data, $fields);

        if ( !empty($values) ) $this->populate($values);

    }

    public function getId() {

        return $this->get('id');

    }

    public function getSchema() {

        return $this->schema;

    }

    public function getFields() {

        return array_keys($this->data);

    }

    public function clone() {

        list($schema, $fields, $values, $database, $configuration) = $this->args;

        $values = array_replace($values, $this->data);

        $className = get_class($this);

        return new $className($schema, $fields, $values, $database, $configuration);

    }

    public function persist() {

    }

    public function delete() {

    }

    protected function create() {

    }

    protected function update() {

    }

    protected function remove() {

    }

    private function populate($values) {

        $diff = array_diff( array_keys($this->data), array_keys($values) );

        if ( !empty($diff) ) throw new Exception("Missing class attributes: ".implode(",",$diff));

        $this->data = array_replace($this->data, $this->value);

    }

}
