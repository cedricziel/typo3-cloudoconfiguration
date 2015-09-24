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

		$vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');

		if (getenv('DYNO')) {
			$logger->info('Detected Heroku Environment');
		}

		file_put_contents(
			$vendorDir . '/../web/typo3conf/AutoConfiguration.php',
			self::getFileTemplate()
		);
	}

	private static function getFileTemplate()
	{

		return "<?php \n"
		. "//Autoconfigutation \n"
		. '\CedricZiel\TYPO3\CloudoConfiguration\HerokuAutoConfiguration::configureDatabase();' . "\n"
		. '\CedricZiel\TYPO3\CloudoConfiguration\HerokuAutoConfiguration::configureSessionStorage();' . "\n"
		. '\CedricZiel\TYPO3\CloudoConfiguration\HerokuAutoConfiguration::relaxBackendIpRestriction();' . "\n";
	}
}
