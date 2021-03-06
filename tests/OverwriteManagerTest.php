<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2020 Robin Appelman <robin@icewind.nl>
 *
 * @license GNU AGPL version 3 or any later version
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\PermissionsOverwrite\Tests;

use OCA\PermissionsOverwrite\OverwriteManager;
use Test\TestCase;

/**
 * @group DB
 */
class OverwriteManagerTest extends TestCase {
	public function testSetGet() {
		/** @var OverwriteManager $manager */
		$manager = \OC::$server->query(OverwriteManager::class);
		$manager->setOverwrite(1, 'foobar', 12);
		$this->assertEquals(12, $manager->getOverwrite(1, 'foobar'));

		$this->assertEquals(null, $manager->getOverwrite(2, 'foobar'));
		$this->assertEquals(null, $manager->getOverwrite(1, 'foobar2'));
	}

	public function testSetGetForMount() {
		/** @var OverwriteManager $manager */
		$manager = \OC::$server->query(OverwriteManager::class);
		$manager->setOverwrite(1, 'foobar', 12);
		$manager->setOverwrite(1, '', 1);
		$manager->setOverwrite(1, 'test', 2);

		$this->assertEquals([
			'' => 1,
			'foobar' => 12,
			'test' => 2
		], $manager->getOverwritesForMount(1));
	}

	public function testSetOverwrite() {
		/** @var OverwriteManager $manager */
		$manager = \OC::$server->query(OverwriteManager::class);
		$manager->setOverwrite(1, 'foobar', 12);
		$this->assertEquals(12, $manager->getOverwrite(1, 'foobar'));
		$manager->setOverwrite(1, 'foobar', 20);
		$this->assertEquals(20, $manager->getOverwrite(1, 'foobar'));
	}

	public function testDelete() {
		/** @var OverwriteManager $manager */
		$manager = \OC::$server->query(OverwriteManager::class);
		$manager->setOverwrite(1, 'foobar', 12);
		$this->assertEquals(12, $manager->getOverwrite(1, 'foobar'));
		$manager->removeOverwrite(1, 'foobar');
		$this->assertEquals(null, $manager->getOverwrite(1, 'foobar'));
	}
}
