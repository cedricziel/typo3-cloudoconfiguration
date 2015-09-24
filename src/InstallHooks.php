<?php

namespace CedricZiel\TYPO3\CloudoConfiguration;

use Monolog\Logger;
use Composer\Script\Event;

class InstallHooks
{

	/**
	 * Dumps the result of an environment scan in the
	 * form of configuration to use the resources,
	 * the environment offers.
	 *
	 * @param Event $event
	 */
	public static function writeAutoConfiguration(Event $event)
	{

		$logger = new Logger(InstallHooks::class);
		$logger->info('Called writeAutoConfiguration()');
	}
}
