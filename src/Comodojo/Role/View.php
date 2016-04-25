<?php namespace Comodojo\Role;

use \Comodojo\Components\ViewTrait;
use \Comodojo\Components\PackageViewTrait;
use \Comodojo\User\Iterator as UserIterator;
use \Comodojo\Application\Iterator as ApplicationIterator;
use \Comodojo\Application\View as ApplicationView;
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

class View extends Model {

    use ViewTrait;

    public function getUsers() {

        return UserIterator::loadByRole($this->configuration(), $this->id, $this->database(), false);

    }

    public function getApplications() {

        return ApplicationIterator::loadByRole($this->configuration(), $this->id, $this->database(), false);

    }

    public function getLandingApp() {

        $application = new ApplicationView($this->configuration(), $this->database());

        return $application->load($this->landingapp);

    }

}
