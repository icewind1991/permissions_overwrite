<?php

/**
 * SPDX-FileCopyrightText: 2017-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Files_External\Service;

use OC\Files\Cache\Storage;
use OC\Files\Filesystem;
use OCA\Files\AppInfo\Application as FilesApplication;
use OCA\Files\ConfigLexicon;
use OCA\Files_External\AppInfo\Application;
use OCA\Files_External\Event\StorageCreatedEvent;
use OCA\Files_External\Event\StorageDeletedEvent;
use OCA\Files_External\Lib\Auth\AuthMechanism;
use OCA\Files_External\Lib\Auth\InvalidAuth;
use OCA\Files_External\Lib\Backend\Backend;
use OCA\Files_External\Lib\Backend\InvalidBackend;
use OCA\Files_External\Lib\DefinitionParameter;
use OCA\Files_External\Lib\StorageConfig;
use OCA\Files_External\NotFoundException;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\Files\Events\InvalidateMountCacheEvent;
use OCP\Files\StorageNotAvailableException;
use OCP\IAppConfig;
use OCP\Server;
use OCP\Util;
use Psr\Log\LoggerInterface;

/**
 * Service class to manage external storage
 *
 * @psalm-import-type ExternalMountInfo from DBConfigService
 */
abstract class StoragesService {
	public function __construct(
		protected BackendService $backendService,
		protected DBConfigService $dbConfig,
		protected IEventDispatcher $eventDispatcher,
		protected IAppConfig $appConfig,
	) {
	}

	/**
	 * @return list<ExternalMountInfo>
	 */
	protected function readDBConfig(): array
    {
    }

	protected function getStorageConfigFromDBMount(array $mount): ?StorageConfig
    {
    }

	/**
	 * Read the external storage config
	 *
	 * @return array<int, StorageConfig> map of storage id to storage config
	 */
	protected function readConfig(): array
    {
    }

	/**
	 * Get a storage with status
	 *
	 * @param int $id storage id
	 *
	 * @throws NotFoundException if the storage with the given id was not found
	 */
	public function getStorage(int $id): StorageConfig
    {
    }

	/**
	 * Check whether this storage service should provide access to a storage
	 */
	abstract protected function isApplicable(StorageConfig $config): bool;

	/**
	 * Gets all storages, valid or not
	 *
	 * @return StorageConfig[] array of storage configs
	 */
	public function getAllStorages(): array
    {
    }

	/**
	 * Gets all valid storages
	 *
	 * @return StorageConfig[]
	 */
	public function getStorages(): array
    {
    }

	/**
	 * Validate storage
	 * FIXME: De-duplicate with StoragesController::validate()
	 *
	 */
	protected function validateStorage(StorageConfig $storage): bool
    {
    }

	/**
	 * Get the visibility type for this controller, used in validation
	 *
	 * @return BackendService::VISIBILITY_*
	 */
	abstract public function getVisibilityType(): int;

	protected function getType(): int
    {
    }

	/**
	 * Add new storage to the configuration
	 *
	 * @param StorageConfig $newStorage storage attributes
	 *
	 * @return StorageConfig storage config, with added id
	 */
	public function addStorage(StorageConfig $newStorage): StorageConfig
    {
    }

	/**
	 * Create a storage from its parameters
	 *
	 * @param string $mountPoint storage mount point
	 * @param string $backendIdentifier backend identifier
	 * @param string $authMechanismIdentifier authentication mechanism identifier
	 * @param array $backendOptions backend-specific options
	 * @param array|null $mountOptions mount-specific options
	 * @param array|null $applicableUsers users for which to mount the storage
	 * @param array|null $applicableGroups groups for which to mount the storage
	 * @param int|null $priority priority
	 *
	 * @return StorageConfig
	 */
	public function createStorage(string $mountPoint, string $backendIdentifier, string $authMechanismIdentifier, array $backendOptions, ?array $mountOptions = null, ?array $applicableUsers = null, ?array $applicableGroups = null, ?int $priority = null): StorageConfig
    {
    }

	/**
	 * Triggers the given hook signal for all the applicables given
	 *
	 * @param string $signal signal
	 * @param string $mountPoint hook mount point param
	 * @param string $mountType hook mount type param
	 * @param array $applicableArray array of applicable users/groups for which to trigger the hook
	 */
	protected function triggerApplicableHooks(string $signal, string $mountPoint, string $mountType, array $applicableArray): void
    {
    }

	/**
	 * Triggers $signal for all applicable users of the given
	 * storage
	 *
	 * @param StorageConfig $storage storage data
	 * @param string $signal signal to trigger
	 */
	abstract protected function triggerHooks(StorageConfig $storage, string $signal): void;

	/**
	 * Triggers signal_create_mount or signal_delete_mount to
	 * accommodate for additions/deletions in applicableUsers
	 * and applicableGroups fields.
	 *
	 * @param StorageConfig $oldStorage old storage data
	 * @param StorageConfig $newStorage new storage data
	 */
	abstract protected function triggerChangeHooks(StorageConfig $oldStorage, StorageConfig $newStorage): void;

	/**
	 * Update storage to the configuration
	 *
	 * @param StorageConfig $updatedStorage storage attributes
	 *
	 * @return StorageConfig storage config
	 * @throws NotFoundException if the given storage does not exist in the config
	 */
	public function updateStorage(StorageConfig $updatedStorage): StorageConfig
    {
    }

	/**
	 * Delete the storage with the given id.
	 *
	 * @param int $id storage id
	 *
	 * @throws NotFoundException if no storage was found with the given id
	 */
	public function removeStorage(int $id): void
    {
    }

	public function updateOverwriteHomeFolders(): void
    {
    }
}
