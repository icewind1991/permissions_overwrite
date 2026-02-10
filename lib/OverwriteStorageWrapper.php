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

namespace OCA\PermissionsOverwrite;

use OC\Files\Storage\Wrapper\Wrapper;
use OCP\Constants;
use OCP\Files\Cache\ICache;
use OCP\Files\Storage\IStorage;

class OverwriteStorageWrapper extends Wrapper {
	private OverwriteSet $overwrites;

	public function __construct(array $parameters) {
		parent::__construct($parameters);
		$this->overwrites = $parameters['overwrites'];
	}

	public function getPermissions(string $path): int {
		$overwrite = $this->overwrites->getOverwriteForPath($path);
		if ($overwrite !== null) {
			return $overwrite;
		}

		return parent::getPermissions($path);
	}

	public function isReadable(string $path): bool {
		return ($this->getPermissions($path) & Constants::PERMISSION_READ) > 0;
	}

	public function isCreatable(string $path): bool {
		return ($this->getPermissions($path) & Constants::PERMISSION_CREATE) > 0;
	}

	public function isUpdatable(string $path): bool {
		return ($this->getPermissions($path) & Constants::PERMISSION_UPDATE) > 0;
	}

	public function isDeletable(string $path): bool {
		return ($this->getPermissions($path) & Constants::PERMISSION_DELETE) > 0;
	}

	public function isSharable(string $path): bool {
		return ($this->getPermissions($path) & Constants::PERMISSION_SHARE) > 0;
	}

	public function getMetaData(string $path): ?array {
		$data = parent::getMetaData($path);

		if ($data && isset($data['permissions'])) {
			$overwrite = $this->overwrites->getOverwriteForPath($path);
			if ($overwrite !== null) {
				$data['scan_permissions'] = isset($data['scan_permissions']) ? $data['scan_permissions'] : $data['permissions'];
				$data['permissions'] = $overwrite;
			}
		}
		return $data;
	}

	public function getCache(string $path = '', ?IStorage $storage = null): ICache {
		if (!$storage) {
			$storage = $this;
		}
		$sourceCache = parent::getCache($path, $storage);
		return new OverwriteCacheWrapper($sourceCache, $this->overwrites);
	}
}
