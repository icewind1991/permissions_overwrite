<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\PermissionsOverwrite\Listener;

use OC\Files\Filesystem;
use OCA\Files_External\Lib\PersonalMount;
use OCA\Files_External\Service\UserGlobalStoragesService;
use OCA\PermissionsOverwrite\AppInfo\Application;
use OCA\PermissionsOverwrite\OverwriteManager;
use OCA\PermissionsOverwrite\OverwriteSet;
use OCA\PermissionsOverwrite\OverwriteStorageWrapper;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Files\Events\BeforeFileSystemSetupEvent;
use OCP\Files\Mount\IMountPoint;
use OCP\Files\Storage\IStorage;
use OCP\IUser;

/** @template-implements IEventListener<BeforeFileSystemSetupEvent> */
class FilesystemSetupListener implements IEventListener {
	public function __construct(
		private readonly OverwriteManager $overwriteManager,
		private readonly UserGlobalStoragesService $userGlobalStoragesService,
	) {

	}

	private function getMountIdForMountPoint(IUser $user, string $mountPoint): ?int {
		foreach ($this->userGlobalStoragesService->getAllStoragesForUser($user) as $storageConfig) {
			$storageMountPoint = rtrim('/' . $user->getUID() . '/files' . $storageConfig->getMountPoint()) . '/';
			if ($storageMountPoint === $mountPoint) {
				return $storageConfig->getId();
			}
		}

		return null;
	}

	public function handle(Event $event): void {
		if (!$event instanceof BeforeFileSystemSetupEvent) {
			return;
		}
		$user = $event->getUser();
		Filesystem::addStorageWrapper(Application::APP_ID, function (string $mountPoint, IStorage $storage, IMountPoint $mount) use ($user) {
			$mountId = $mount->getMountId();
			// work around mount id not being set on personal mounts
			if ($mountId === null && $mount instanceof PersonalMount) {
				$mountId = $this->getMountIdForMountPoint($user, $mount->getMountPoint());
			}

			if ($mountId) {
				$overwrites = new OverwriteSet($this->overwriteManager->getOverwritesForMount($mountId));
				return new OverwriteStorageWrapper([
					'storage' => $storage,
					'overwrites' => $overwrites,
				]);
			} else {
				return $storage;
			}
		});
	}


}
