<?php

/**
 * SPDX-FileCopyrightText: 2019-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Files_External\Service;

use OC\Files\Filesystem;
use OCA\Files_External\Event\StorageCreatedEvent;
use OCA\Files_External\Event\StorageDeletedEvent;
use OCA\Files_External\Event\StorageUpdatedEvent;
use OCA\Files_External\Lib\StorageConfig;
use OCA\Files_External\MountConfig;
use OCP\IGroup;
use Override;

/**
 * Service class to manage global external storage
 */
class GlobalStoragesService extends StoragesService {
	#[Override]
    protected function triggerHooks(StorageConfig $storage, $signal): void
    {
    }

	#[Override]
    protected function triggerChangeHooks(StorageConfig $oldStorage, StorageConfig $newStorage): void
    {
    }

	#[Override]
    public function getVisibilityType(): int
    {
    }

	#[Override]
    protected function isApplicable(StorageConfig $config): bool
    {
    }

	/**
	 * Get all configured admin and personal mounts
	 *
	 * @return StorageConfig[] map of storage id to storage config
	 */
	public function getStorageForAllUsers(): array
    {
    }

	/**
	 * Gets all storages for the group, not including any global storages
	 * @return StorageConfig[]
	 */
	public function getAllStoragesForGroup(IGroup $group): array
    {
    }

	/**
	 * @return StorageConfig[]
	 */
	public function getAllGlobalStorages(): array
    {
    }
}
