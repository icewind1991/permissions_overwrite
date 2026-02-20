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

class OverwriteSet {
	private array $overwrites;

	public function __construct(array $overwrites) {
		$paths = array_keys($overwrites);
		$permissions = array_values($overwrites);
		$paths = array_map(function ($path) {
			return $path . '/';
		}, $paths);
		$overwrites = array_combine($paths, $permissions);

		ksort($overwrites);

		$this->overwrites = $overwrites;
	}

	public function getOverwriteForPath(string $path): ?int {
		$path = trim($path, '/') . '/';
		$overwrite = null;

		// note that because the overwrites are sorted by path, later matching iterations are always subfolders of the previous match
		foreach ($this->overwrites as $overwritePath => $permission) {
			if ($overwritePath === '/' || str_starts_with($path, $overwritePath)) {
				$overwrite = $permission;
			}
		}

		return $overwrite;
	}
}
