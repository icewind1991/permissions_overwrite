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
use OCA\PermissionsOverwrite\OverwriteManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveCommand extends Base {
	public function __construct(
		private readonly OverwriteManager $overwriteManager,
	) {
		parent::__construct();
	}

	protected function configure(): void {
		$this
			->setName('permissions_overwrite:remove')
			->setDescription('Remove a configured overwrite')
			->addArgument(
				'mount_id',
				InputArgument::REQUIRED,
				'The id of the mountpoint to remove the overwrite for'
			)->addArgument(
				'path',
				InputArgument::REQUIRED,
				'The path in the mountpoint to remove the overwrite for'
			);
		parent::configure();
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$mountId = (int)$input->getArgument('mount_id');

		$path = Filesystem::normalizePath($input->getArgument('path'));
		$path = trim($path, '/');

		$this->overwriteManager->removeOverwrite($mountId, $path);

		return 0;
	}
}
