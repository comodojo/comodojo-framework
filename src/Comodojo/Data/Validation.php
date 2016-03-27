<?php namespace Comodojo\Data;

use \UnexpectedValueException;
use \InvalidArgumentException;

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

class Validation {

    const STRING = 'STRING';
    const REGEX = 'STRING';
    const BOOL = 'BOOL';
    const BOOLEAN = 'BOOL';
    const INT = 'INT';
    const INTEGER = 'INT';
    const NUMBER = 'NUMBER';
    const DOUBLE = 'DOUBLE';
    const FLOAT = 'FLOAT';
    const JSON = 'JSON';
    const SERIALIZED = 'SERIALIZED';

    private $supported_types = array (
        "STRING" => 'self::validateString',
        "REGEX" => 'self::validateRegex',
        "BOOL" => 'self::validateBoolean',
        "BOOLEAN" => 'self::validateBoolean',
        "INT" => 'self::validateInteger',
        "INTEGER" => 'self::validateInteger',
        "NUMBER" => 'self::validateNumeric',
        "DOUBLE" => 'self::validateFloat',
        "FLOAT" => 'self::validateFloat',
        "JSON" => 'self::validateJson',
        "SERIALIZED" => 'self::validateSerialized'
    );

    public static function validate($data, $type, $filter=null) {

        $type = strtoupper($type);

        if ( !array_key_exists($type, $this->supported_types) ) {
            throw new UnexpectedValueException("Bad validation type");
        }

        if ( call_user_func($this->supported_types[$type], $data, $filter) === false ) {
            throw new InvalidArgumentException("Bad value $data for a $type");
        }

        return true;

    }

    public static function validateString($data, $filter=null) {
        return is_string($data);
    }

    public static function validateRegex($data, $filter=null) {
        return preg_match($filter, $data);
    }

    public static function validateBoolean($data, $filter=null) {
        return is_bool($data);
    }

    public static function validateInteger($data, $filter=null) {
        return is_int($data);
    }

    public static function validateNumeric($data, $filter=null) {
        return is_numeric($data);
    }

    public static function validateFloat($data, $filter=null) {
        return is_float($data);
    }

    public static function validateJson($data, $filter=null) {
        $decoded = json_decode($data);
        return !is_null($decoded);
    }

    public static function validateSerialized($data, $filter=null) {
        $decoded = unserialize($data);
        return ($decoded == serialize(false) || $decoded !== false);
    }

}
