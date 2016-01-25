<?php namespace Comodojo\Settings;

use \Comodojo\Database\Database;
use \Comodojo\Base\Element;
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

class Setting extends Element {

    protected $value    = "";

    protected $constant = false;

    protected $type     = "STRING";

    protected $valid    = "";

    public function getValue() {
        
        if (!$this->validate()) {
            
            throw new ConfigurationException(sprintf("The value for '%s' is unreadable", $this->getName()));
            
        }
        
        $type = $this->type;
        
        $val  = $this->value;
        
        switch ($type) {
            
            case "STRING":
                
                return $val . "";
                    
                break;
                
            case "BOOL":
            case "BOOLEAN":
                
                return filter_var($val, FILTER_VALIDATE_BOOLEAN);
                    
                break;
                
            case "INT":
            case "INTEGER":
            case "NUMBER":
                
                return intval($val);
                    
                break;
                
            case "DOUBLE":
                
                return doubleval($val);
                    
                break;
                
            case "FLOAT":
                
                return floatval($val);
                    
                break;
                
            case "JSON":
                
                return json_decode($val, true);
                    
                break;
                
            case "OBJECT":
                
                return unserialize($val);
                    
                break;
        }
        
        return null;

    }

    public function setValue($value, $type = "") {
        
        if (!empty($type)) {
            
            $this->setType($type);
            
        }
        
        $type = $this->type;
        
        switch ($type) {
            
            case "STRING":
                
                $this->value = strval($value);
                    
                break;
                
            case "BOOL":
            case "BOOLEAN":
                
                $this->value = (filter_var($value, FILTER_VALIDATE_BOOLEAN))?"1":"0";
                    
                break;
                
            case "INT":
            case "INTEGER":
            case "NUMBER":
                
                $this->value = sprintf("%d", intval($value));
                    
                break;
                
            case "DOUBLE":
                
                $this->value = sprintf("%e", doubleval($value));
                    
                break;
                
            case "FLOAT":
                
                $this->value = sprintf("%f", floatval($value));
                    
                break;
                
            case "JSON":
                
                $this->value = json_encode($value);
                    
                break;
                
            case "OBJECT":
                
                $this->value = serialize($value);
                    
                break;
        }

        return $this;

    }

    public function isConstant() {

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
        
        $val  = $this->getValue();
        
        $type = strtoupper($type);
        
        if ($type == "STRING"  ||
            $type == "BOOL"    ||
            $type == "BOOLEAN" ||
            $type == "INT"     ||
            $type == "INTEGER" ||
            $type == "NUMBER"  ||
            $type == "DOUBLE"  ||
            $type == "FLOAT"   ||
            $type == "JSON"    ||
            $type == "OBJECT"
        ) {

            $this->type = $type;
            
        } else {
            
            throw new ConfigurationException("Setting type not supported");
            
        }
        
        $this->setValue($val);

        return $this;

    }

    public function getValidation() {

        return $this->valid;

    }

    public function setValidation($validation) {

        $this->valid = $validation;

        return $this;

    }

    public function validate() {
        
        $type = $this->type;
        
        $val  = $this->value;
        
        switch ($type) {
            
            case "STRING":
                if (preg_match("/" . $this->getValidation() . "/i", $val))
                    return true;
                    
                break;
                
            case "BOOL":
            case "BOOLEAN":
                if ($val == "0" || $val == "1" || $val == "true" || $val == "false")
                    return true;
                    
                break;
                
            case "INT":
            case "INTEGER":
            case "NUMBER":
                if (is_numeric($val))
                    return true;
                    
                break;
                
            case "DOUBLE":
            case "FLOAT":
                if (is_float($val))
                    return true;
                    
                break;
                
            case "JSON":
                $decoded = json_decode($val);
                if (!is_null($decoded))
                    return true;
                    
                break;
                
            case "OBJECT":
                $decoded = unserialize($val);
                if ($val == serialize(false) || $decoded !== false)
                    return true;
                    
                break;
        }
        
        return false;
    }

    public static function load($id, $dbh) {

        $query = sprintf("SELECT * FROM comodojo_settings WHERE id = %d",
            $id
        );

        try {

            $result = $dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        if ($result->getLength() > 0) {

            $data = $result->getData();

            $data = array_values($data[0]);

            $setting = new Setting($dbh);

            $setting->setData($data);

            return $setting;

        }

    }

    protected function getData() {

        return array(
            $this->id,
            $this->name,
            $this->value,
            $this->constant,
            $this->type,
            $this->valid,
            $this->package
        );

    }

    protected function setData($data) {

        $this->id       = intval($data[0]);
        $this->name     = $data[1];
        $this->value    = $data[2];
        $this->constant = filter_var($data[3], FILTER_VALIDATE_BOOLEAN);
        $this->type     = strtoupper($data[4]);
        $this->valid    = $data[5];
        $this->package  = $data[6];

        return $this;

    }

    protected function create() {

        $query = sprintf("INSERT INTO comodojo_settings VALUES (0, '%s', '%s', %d, '%s', '%s', '%s')",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->value),
            (($this->constant)?1:0),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->type),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->valid),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->package)
        );

        try {

            $result = $this->dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->id = $result->getInsertId();

        return $this;

    }

    protected function update() {

        $query = sprintf("UPDATE comodojo_settings SET name = '%s', value = '%s', `constant` = %d, `type` = '%s', validation = '%s', package = '%s' WHERE id = %d",
            mysqli_real_escape_string($this->dbh->getHandler(), $this->name),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->value),
            (($this->constant)?1:0),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->type),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->valid),
            mysqli_real_escape_string($this->dbh->getHandler(), $this->package),
            $this->id
        );

        try {

            $this->dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        return $this;

    }

    public function delete() {

        $query = sprintf("DELETE FROM comodojo_settings WHERE id = %d",
            $this->id
        );

        try {

            $this->dbh->query($query);


        } catch (DatabaseException $de) {

            throw $de;

        }

        $this->setData(array(0, "", "", false, "", "", ""));

        return $this;

    }

}
