<?php namespace Comodojo\Theme;

use \Comodojo\Components\ComodojoIterator;
use \Comodojo\Components\IteratorLoaderTrait;
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

class Iterator extends ComodojoIterator {

    use ThemeTrait;
    use IteratorLoaderTrait;

    public function __construct(
        Configuration $configuration,
        EnhancedDatabase $database = null,
        $controller = false
    ) {

        parent::__construct(
            $configuration,
            self::$element_schema,
            array_keys(self::$element_attributes),
            $controller === true ? self::$element_controller : self::$element_view,
            $database
        );

    }

}
