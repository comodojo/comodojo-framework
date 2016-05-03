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

    protected $schema;

    public function __construct(
        Configuration $configuration,
        $schema,
        $attributes,
        $values = array(),
        EnhancedDatabase $database = null
    ) {

        $this->args = func_get_args();

        parent::__construct($configuration, $database);

        $this->data['id'] = 0;

        $this->schema = $schema;

        $this->data = array_merge($this->data, $attributes);

        if ( !empty($values) ) $this->populate($values);

    }

    public function getSchema() {

        return $this->schema;

    }

    public function getData() {

        return array_keys($this->data);

    }

    public function __clone() {

        list($configuration, $schema, $attributes, $values, $database) = $this->args;

        $values = array_replace($values, $this->data);

        $values['id'] = 0;

        $className = get_class($this);

        return new $className($configuration, $database, $values);

    }

    public function load($id) {

        $fid = filter_var($id, FILTER_VALIDATE_INT);

        if ( $fid === false ) {
            $className = get_class($this);
            throw new Exception("Invalid id $id for $className");
        }

        try {

            $result = $this->database
                ->table($this->schema)
                ->keys(array_keys($this->data))
                ->where('id', '=', $fid)
                ->get();

            if ( $result->getLength() != 1 ) {
                $className = get_class($this);
                throw new Exception("Unable to load object $className: missing id $id");
            }

            $data = $result->getData();

            $return = $this->populate($data[0]);

        } catch (DatabaseException $de) {
            throw $de;
        } catch (Exception $e) {
            throw $e;
        }

        return $return;

    }

    protected function create() {

        try {

            $result = $this->database
                ->table($this->schema)
                ->keys(array_keys($this->data))
                ->values(array_values($this->data))
                ->store();

            $id = $result->getInsertId();

            $this->data['id'] = $id;

        } catch (DatabaseException $de) {
            throw $de;
        } catch (Exception $e) {
            throw $e;
        }

        return $this;

    }

    protected function update() {

        try {

            $result = $this->database
                ->table($this->schema)
                ->keys(array_keys($this->data))
                ->values(array_values($this->data))
                // ->keys(array_keys($this->args))
                // ->values(array_values(array_intersect($this->data, $this->args)))
                ->where('id', '=', $this->id)
                ->update();

        } catch (DatabaseException $de) {
            throw $de;
        } catch (Exception $e) {
            throw $e;
        }

        return $this;

    }

    protected function remove() {

        try {

            $result = $this->database
                ->table($this->schema)
                ->where('id', '=', $this->id)
                ->delete();

            $rows = $result->getAffectedRows();

        } catch (DatabaseException $de) {
            throw $de;
        } catch (Exception $e) {
            throw $e;
        }

        return intval($rows) == 1 ? true : false;

    }

    protected function populate($values) {

        $diff = array_diff( array_keys($this->data), array_keys($values) );

        if ( !empty($diff) ) throw new Exception("Missing class attributes: ".implode(",",$diff));

        //$this->data = array_replace($this->data, $values);

        foreach ($values as $key => $value) {
            $this->$key = $value;
        }

        return $this;

    }

}
