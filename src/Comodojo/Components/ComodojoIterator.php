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

abstract class ComodojoIterator extends AbstractIterator {

    protected $schema;

    protected $fields;

    public function __construct(
        Configuration $configuration,
        $schema,
        $fields,
        $className,
        EnhancedDatabase $database = null
    ) {

        parent::__construct($configuration, $database);

        $this->schema = $schema;

        $this->className = $className;

        $this->fields = array_merge(array('id'), $fields);

    }

    public function getSchema() {

        return $this->schema;

    }

    public function getFields() {

        return $this->fields;

    }

    public function loadData() {

        try {

            $result = $this->database
                ->table($this->schema)
                ->keys($this->fields)
                ->get();

            $data = $result->getData();

            $this->populate($data);

        } catch (DatabaseException $de) {
            throw $de;
        } catch (Exception $e) {
            throw $e;
        }

        return $this;

    }

    public function loadFiltered($filters) {

        try {

            $result = $this->database
                ->table($this->schema)
                ->keys($this->fields)
                ->where($filters)
                ->get();

            $data = $result->getData();

            $this->populate($data);

        } catch (DatabaseException $de) {
            throw $de;
        } catch (Exception $e) {
            throw $e;
        }

        return $this;

    }

    protected function populate($data) {

        foreach ($data as $element) {

            $instance = new $this->className(
                $this->configuration,
                //$this->schema,
                //$this->fields,
                $this->database,
                $element
            );

            $this->data[] = $instance;

        }

    }

}
