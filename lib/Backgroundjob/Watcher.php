<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2021 Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @author Roeland Jago Douma <roeland@famdouma.nl>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\TwoFactorReminder\Backgroundjob;

use OCA\TwoFactorReminder\Service\Check2FA;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\TimedJob;
use OCP\IUser;
use OCP\IUserManager;

class Watcher extends TimedJob {
	/** @var IUserManager */
	private $userManager;

	/** @var Check2FA */
	private $check2FA;

	public function __construct(ITimeFactory $timeFactory, IUserManager $userManager, Check2FA $check2FA) {
		parent::__construct($timeFactory);

		// Run once every 30 days
		$this->setInterval(2592000);

		$this->userManager = $userManager;
		$this->check2FA = $check2FA;
	}

	protected function run($argument) {
		$this->userManager->callForSeenUsers(function (IUser $user): bool {
			$this->check2FA->processUser($user);
			return true;
		});
	}


}
