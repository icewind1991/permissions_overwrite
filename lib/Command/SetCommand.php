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
use OC\Files\Filesystem;
use OCA\Files_External\NotFoundException;
use OCA\Files_External\Service\GlobalStoragesService;
use OCA\PermissionsOverwrite\OverwriteManager;
use OCP\Constants;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetCommand extends Base {
	public function __construct(
		private readonly OverwriteManager $overwriteManager,
		private readonly GlobalStoragesService $storagesService,
	) {
		parent::__construct();
	}

	protected function configure(): void {
		$this
			->setName('permissions_overwrite:set')
			->setDescription('Set a permission overwrite')
			->addArgument(
				'mount_id',
				InputArgument::REQUIRED,
				'The id of the mountpoint to overwrite the permissions for'
			)->addArgument(
				'path',
				InputArgument::REQUIRED,
				'The path in the mountpoint to overwrite the permissions for'
			)->addArgument(
				'permissions',
				InputArgument::REQUIRED,
				'The permissions to set, either ALL, READONLY or NONE'
			);
		parent::configure();
	}

	private function parsePermissions(string $permissions): ?int {
		switch ($permissions) {
			case 'ALL':
				return Constants::PERMISSION_ALL;
			case 'READONLY':
				return Constants::PERMISSION_READ + Constants::PERMISSION_SHARE;
			case 'NONE':
				return 0;
			default:
				if (is_numeric($permissions)) {
					return ((int)$permissions) & Constants::PERMISSION_ALL;
				} else {
					return null;
				}
		}
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$mountId = (int)$input->getArgument('mount_id');

		try {
			$this->storagesService->getStorage($mountId);
		} catch (NotFoundException $e) {
			$output->writeln("<error>Mount with id $mountId not found, use `occ files_external:list` to list all existing mounts</error>");

			return -1;
		}

		$path = Filesystem::normalizePath($input->getArgument('path'));
		$path = trim($path, '/');

		$permissions = $this->parsePermissions($input->getArgument('permissions'));
		if ($permissions === null) {
			$output->writeln('<error>Invalid permission input, provide either ALL, READONLY or NONE</error>');

			return -1;
		}

		$this->overwriteManager->setOverwrite($mountId, $path, $permissions);

		return 0;
	}
}
