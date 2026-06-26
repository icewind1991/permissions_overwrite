<?php

/**
 * SPDX-FileCopyrightText: 2016-2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-FileCopyrightText: 2016 ownCloud, Inc.
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace OCA\Files_External\Service;

use OCA\Files_External\Lib\StorageConfig;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IAppConfig;
use OCP\IGroupManager;
use OCP\IUser;
use OCP\IUserSession;
use Override;

/**
 * Service class to read global storages applicable to the user
 * Read-only access available, attempting to write will throw DomainException
 */
class UserGlobalStoragesService extends GlobalStoragesService {
	use UserTrait;

	public function __construct(BackendService $backendService, DBConfigService $dbConfig, IUserSession $userSession, protected IGroupManager $groupManager, IEventDispatcher $eventDispatcher, IAppConfig $appConfig)
    {
    }

	#[Override]
    protected function readDBConfig(): array
    {
    }

	#[Override]
    public function addStorage(StorageConfig $newStorage): never
    {
    }

	#[Override]
    public function updateStorage(StorageConfig $updatedStorage): never
    {
    }

	#[Override]
    public function removeStorage(int $id): never
    {
    }

	/**
	 * Get unique storages, in case two are defined with the same mountpoint
	 * Higher priority storages take precedence
	 *
	 * @return StorageConfig[]
	 */
	public function getUniqueStorages(): array
    {
    }

	/**
	 * Get a priority 'type', where a bigger number means higher priority
	 * user applicable > group applicable > 'all'
	 *
	 * @param StorageConfig $storage
	 * @return int
	 */
	protected function getPriorityType(StorageConfig $storage): int
    {
    }

	#[\Override]
    protected function isApplicable(StorageConfig $config): bool
    {
    }

	/**
	 * Gets all storages for the user, admin, personal, global, etc
	 *
	 * @param IUser|null $user user to get the storages for, if not set the currently logged in user will be used
	 * @return StorageConfig[] array of storage configs
	 */
	public function getAllStoragesForUser(?IUser $user = null): array
    {
    }

	/**
	 * @return StorageConfig[]
	 */
	public function getAllStoragesForUserWithPath(string $path, bool $forChildren): array
    {
    }
}
