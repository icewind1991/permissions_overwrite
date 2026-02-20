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

namespace OCA\PermissionsOverwrite\Command;

use OC\Core\Command\Base;
use OCA\PermissionsOverwrite\OverwriteManager;
use OCP\Constants;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Base {
	public function __construct(
		private readonly OverwriteManager $overwriteManager,
	) {
		parent::__construct();
	}


	protected function configure(): void {
		$this
			->setName('permissions_overwrite:list')
			->setDescription('List all configured overwrites');
		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$overwrites = $this->overwriteManager->getAll();

		$overwrites = array_map(function ($pathPermissions) {
			$paths = array_keys($pathPermissions);
			$permissions = array_values($pathPermissions);

			$paths = array_map(function (string $path) {
				return ($path === '') ? '/' : $path;
			}, $paths);
			$permissions = array_map(function (int $permission) {
				switch ($permission) {
					case Constants::PERMISSION_ALL:
						return 'ALL';
					case Constants::PERMISSION_READ + Constants::PERMISSION_SHARE:
						return 'READONLY';
					case 0:
						return 'NONE';
					default:
						return $permission;
				}
			}, $permissions);
			return array_combine($paths, $permissions);
		}, $overwrites);

		$this->writeArrayInOutputFormat($input, $output, $overwrites);

		return 0;
	}
}
