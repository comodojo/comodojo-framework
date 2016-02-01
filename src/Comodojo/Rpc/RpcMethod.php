<?php namespace Comodojo\Rpc;

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

class RpcMethod extends Element {

    protected $callback = "";

    protected $method = "";

    protected $description = "";

    protected $signatures = array();

    public function getCallback() {

        return $this->callback;

    }

    public function setCallback($callback) {

        $this->callback = $callback;

        return $this;

    }

    public function getMethod() {

        return $this->method;

    }

    public function setMethod($method) {

        $this->method = $method;

        return $this;

    }

    public function getDescription() {

        return $this->description;

    }

    public function setDescription($description) {

        $this->description = $description;

        return $this;

    }

    public function getSignature($index) {

        if (isset($this->signatures[$index])) {

            return $this->signatures[$index];

        }

        return null;

    }

    public function getSignatures() {

        return $this->signatures;

    }

    public function setSignatures($signatures) {

        $this->signatures = $signatures;

        return $this;

    }

    public function getRawSignatures() {

        $signatures = array();

        foreach ($this->signatures as $sig) {

            array_push(
                $signatures,
                array(
                    'returnType' => $sig->getReturnType(),
                    'parameters' => $sig->getRawParameters()
                )
            );

        }

        return $signatures;

    }

    public function setRawSignatures($signatures) {

        foreach ($signatures as $sig) {

            $signature = new RpcSignature();

            $signature->setReturnType($sig['returnType']);
            $signature->setRawParameters($sig['parameters']);

            array_push(
                $this->signatures,
                $signature
            );

        }

        return $this;

    }

    public function addSignature($signature) {

        array_push(
            $this->signatures,
            $signature
        );

        return $this;

    }

    public function addRawSignature($rawSignature) {

        $signature = new RpcSignature();

        $signature->setReturnType($rawSignature['returnType']);
        $signature->setRawParameters($rawSignature['parameters']);

        array_push(
            $this->signatures,
            $signature
        );

        return $this;

    }

    public function removeSignature($index) {

        if (isset($this->signatures[$index])) {

            array_splice($this->signatures, $index, 1);

        }

        return $this;

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

            $rpc = new Rpc($dbh);

            $rpc->setData($data);
            
        } else {
            
            throw new Exception("Unable to load task");
            
        }
        
        return $rpc;

    }

    protected function getData() {

        return array(
            $this->id,
            $this->name,
            $this->callback,
            $this->method,
            $this->description,
            json_encode($this->getRawSignatures()),
            $this->package
        );

    }

    protected function setData($data) {

        $this->id = intval($data[0]);
        $this->name = $data[1];
        $this->callback = $data[2];
        $this->method = $data[3];
        $this->description = $data[4];
        $this->setRawSignatures(json_decode($data[5], true));
        $this->package = $data[6];

        return $this;

    }

    protected function create() {
        
        try {

            $result = Model::create(
                $this->database,
                $this->name,
                $this->callback,
                $this->method,
                $this->description,
                json_encode( $this->getRawSignatures() ),
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
                $this->callback,
                $this->method,
                $this->description,
                json_encode( $this->getRawSignatures() ),
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

        $this->setData(array(0, "", "", "", "", [], ""));

        return $this;

    }

}
