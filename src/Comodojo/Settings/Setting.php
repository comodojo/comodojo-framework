<?php namespace Comodojo\Settings;

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

class Setting extends Element {

    protected $value = "";

    protected $constant = false;

    protected $type = "STRING";

    protected $validation = "";
    
    private $supported_value_types = array(
        "STRING",
        "BOOL",
        "BOOLEAN",
        "INT",
        "INTEGER",
        "NUMBER",
        "DOUBLE",
        "FLOAT",
        "JSON",
        "OBJECT"
    );

    public function getValue() {
        
        return $this->value;

    }

    public function setValue($value) {
        
        $this->value = $value;
        
        return $this;

    }

    public function getConstant() {

        return $this->constant;

    }

    public function setConstant($constant) {

        $this->constant = filter_var($constant, FILTER_VALIDATE_BOOLEAN);

        return $this;

    }

    public function getType() {

        return $this->type;

    }

    public function setType($type) {
        
        $type = strtoupper($type);
        
        if ( !in_array($type, $this->supported_value_types) ) {
            
            throw new Exception("Setting type not supported");
            
        }
        
        $this->type = $type;
            
        return $this;

    }

    public function getValidation() {

        return $this->validation;

    }

    public function setValidation($validation) {

        $this->validation = $validation;

        return $this;

    }

    public function validate() {
        
        $type = $this->type;
        
        $val  = $this->value;
        
        switch ($type) {
            
            case "STRING":
                
                if (preg_match("/" . $this->getValidation() . "/i", $val)) return true;
                    
                break;
                
            case "BOOL":
            case "BOOLEAN":
                
                if ( is_bool($val) ) return true;
                
                break;
                
            case "INT":
            case "INTEGER":
            case "NUMBER":
                
                if (is_numeric($val)) return true;
                    
                break;
                
            case "DOUBLE":
            case "FLOAT":
                
                if (is_float($val)) return true;
                    
                break;
                
            case "JSON":
                
                $decoded = json_decode($val);
                
                if (!is_null($decoded)) return true;
                    
                break;
                
            case "OBJECT":
                
                $decoded = unserialize($val);
                
                if ($val == serialize(false) || $decoded !== false) return true;
                    
                break;
                
        }
        
        return false;
        
    }

    public static function load(EnhancedDatabase $database, $id) {

        try {

            $result = Model::load($database, $id);

        } catch (DatabaseException $de) {

            throw $de;

        }

        if ($result->getLength() > 0) {

            $data = $result->getData();

            $data = array_values($data[0]);

            $setting = new Setting($dbh);

            $setting->setData($data);

        } else {
            
            throw new Exception("Unable to load setting");
            
        }
        
        return $setting;

    }

    protected function getData() {

        return array(
            $this->id,
            $this->name,
            self::encode($this->value),
            $this->constant,
            $this->type,
            $this->valid,
            $this->package
        );

    }

    protected function setData($data) {

        $this->id       = intval($data[0]);
        $this->name     = $data[1];
        $this->value    = self::decode($data[2]);
        $this->constant = filter_var($data[3], FILTER_VALIDATE_BOOLEAN);
        $this->type     = strtoupper($data[4]);
        $this->valid    = $data[5];
        $this->package  = $data[6];

        return $this;

    }

    protected function create() {

        if ( $this->validate() === false ) {
            
            throw new Exception('Value is not valid');
            
        }
        
        $value = self::encode($this->value);

        try {

            $result = Model::create(
                $this->database,
                $this->name,
                $value,
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

        if ( $this->validate() === false ) {
            
            throw new Exception('Value is not valid');
            
        }
        
        $value = self::encode($this->value);

        try {

            $result = Model::create(
                $this->database,
                $this->id,
                $this->name,
                $value,
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

            $result = Model::delete($this->database, $this->id);

        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->setData(array(0, "", "", false, "", "", ""));

        return $this;

    }
    
    private static function encode($value, $type) {
        
        switch ($type) {
            
            case "STRING":
                
                $return = strval($value);
                    
                break;
                
            case "BOOL":
            case "BOOLEAN":
                
                $return = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    
                break;
                
            case "INT":
            case "INTEGER":
            case "NUMBER":
                
                $return = sprintf("%d", intval($value));
                    
                break;
                
            case "DOUBLE":
                
                $return = sprintf("%e", doubleval($value));
                    
                break;
                
            case "FLOAT":
                
                $return = sprintf("%f", floatval($value));
                    
                break;
                
            case "JSON":
                
                $return = json_encode($value);
                    
                break;
                
            case "OBJECT":
                
                $return = serialize($value);
                    
                break;
        }

        return $return;
        
    }
    
    private static function decode($value, $type) {
     
        switch ($type) {
            
            case "STRING":
                
                $return = $value . "";
                    
                break;
                
            case "BOOL":
            case "BOOLEAN":
                
                $return = filter_var($val, FILTER_VALIDATE_BOOLEAN);
                    
                break;
                
            case "INT":
            case "INTEGER":
            case "NUMBER":
                
                $return = intval($val);
                    
                break;
                
            case "DOUBLE":
                
                $return = doubleval($val);
                    
                break;
                
            case "FLOAT":
                
                $return = floatval($val);
                    
                break;
                
            case "JSON":
                
                $return = json_decode($val, true);
                    
                break;
                
            case "OBJECT":
                
                $return = unserialize($val);
                    
                break;
                
        }
        
        return $return;
        
    }

}
