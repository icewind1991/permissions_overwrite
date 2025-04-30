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

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class OverwriteManager {
	private IDBConnection $connection;

	public function __construct(IDBConnection $connection) {
		$this->connection = $connection;
	}

	public function getOverwrite(int $mountId, string $path): ?int {
		$query = $this->connection->getQueryBuilder();

		$query->select('permissions')
			->from('permissions_overwrite')
			->where($query->expr()->eq('mount_id', $query->createNamedParameter($mountId, IQueryBuilder::PARAM_INT)))
			->andWhere($query->expr()->eq('path_hash', $query->createNamedParameter(md5($path))));
		$permissions = $query->executeQuery()->fetchColumn();
		return $permissions === false ? null : (int)$permissions;
	}

	public function getOverwritesForMount(int $mountId): array {
		$query = $this->connection->getQueryBuilder();

		$query->select('path', 'permissions')
			->from('permissions_overwrite')
			->where($query->expr()->eq('mount_id', $query->createNamedParameter($mountId, IQueryBuilder::PARAM_INT)));
		$overwrites = array_column($query->executeQuery()->fetchAll(\PDO::FETCH_NUM), 1, 0);
		return array_map(function ($permission) {
			return (int)$permission;
		}, $overwrites);
	}

	public function setOverwrite(int $mountId, string $path, int $permissions) {
		$query = $this->connection->getQueryBuilder();

		try {
			$query->insert('permissions_overwrite')
				->values([
					'mount_id' => $query->createNamedParameter($mountId, IQueryBuilder::PARAM_INT),
					'path' => $query->createNamedParameter($path),
					'path_hash' => $query->createNamedParameter(md5($path)),
					'permissions' => $query->createNamedParameter($permissions, IQueryBuilder::PARAM_INT),
				]);
			$query->executeStatement();
		} catch (Exception|UniqueConstraintViolationException $e) {
			if (!$e->getPrevious() instanceof UniqueConstraintViolationException) {
				throw $e;
			}
			$query = $this->connection->getQueryBuilder();

			$query->update('permissions_overwrite')
				->set('permissions', $query->createNamedParameter($permissions, IQueryBuilder::PARAM_INT))
				->where($query->expr()->eq('mount_id', $query->createNamedParameter($mountId, IQueryBuilder::PARAM_INT)))
				->andWhere($query->expr()->eq('path_hash', $query->createNamedParameter(md5($path))));
			$query->executeStatement();
		}
	}

	public function removeOverwrite(int $mountId, string $path) {
		$query = $this->connection->getQueryBuilder();

		$query->delete('permissions_overwrite')
			->where($query->expr()->eq('mount_id', $query->createNamedParameter($mountId, IQueryBuilder::PARAM_INT)))
			->andWhere($query->expr()->eq('path_hash', $query->createNamedParameter(md5($path))));
		$query->executeStatement();
	}

	public function getAll(): array {
		$query = $this->connection->getQueryBuilder();

		$query->select('mount_id', 'path', 'permissions')
			->from('permissions_overwrite');

		$result = $query->executeQuery();

		$mounts = [];
		while ($row = $result->fetch()) {
			if (!isset($mounts[$row['mount_id']])) {
				$mounts[$row['mount_id']] = [];
			}
			$mounts[$row['mount_id']][$row['path']] = (int)$row['permissions'];
		}

		return $mounts;
	}
}
