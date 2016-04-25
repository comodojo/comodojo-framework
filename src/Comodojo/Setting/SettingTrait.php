<?php namespace Comodojo\Setting;

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

trait SettingTrait {

    private static $element_schema = "settings";

    private static $element_attributes = array(
        "name" => null,
        "value" => null,
        "constant" => false,
        "type" => null,
        "validation" => null,
        "package" => null
    );

    private static $element_controller = "\\Comodojo\\Setting\\Controller";

    private static $element_view = "\\Comodojo\\Setting\\View";

    private static $element_name = 'name';

}
