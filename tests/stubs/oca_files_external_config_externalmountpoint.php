<?php

/**
 * SPDX-FileCopyrightText: 2017 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Files_External\Config;

use OC\Files\Mount\MountPoint;
use OCA\Files_External\Lib\Auth\Password\SessionCredentials;
use OCA\Files_External\Lib\StorageConfig;

class ExternalMountPoint extends MountPoint {

	public function __construct(protected StorageConfig $storageConfig, $storage, $mountpoint, $arguments = null, $loader = null, $mountOptions = null, $mountId = null)
    {
    }

	#[\Override]
    public function getMountType()
    {
    }

	public function getStorageConfig(): StorageConfig
    {
    }
}
