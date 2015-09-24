<?php

namespace CedricZiel\TYPO3\CloudoConfiguration;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class HerokuAutoConfiguration
 *
 * Large parts taken from mojocode/herokuish
 *
 * @author Morton Jonuschat <m.jonuschat@mojocode.de>
 * @author Cedric Ziel <cedric@cedric-ziel.com>
 *
 * @package CedricZiel\TYPO3\CloudoConfiguration
 */
class HerokuAutoConfiguration
{

	static public function configureAll()
	{

		if (!preg_match('#/Heroku$#', GeneralUtility::getApplicationContext())) {
			return;
		}
		static::configureDatabase();
		static::configureSessionStorage();
		static::relaxBackendIpRestriction();
	}

	static public function configureDatabase()
	{

		if (!empty($_ENV['DATABASE_URL'])) {
			static::updateDatabaseConfiguration($_ENV['DATABASE_URL']);
		} elseif (!empty($_ENV['CLEARDB_DATABASE_URL'])) {
			static::updateDatabaseConfiguration($_ENV['CLEARDB_DATABASE_URL']);
		} elseif (!empty($_ENV['JAWSDB_URL'])) {
			static::updateDatabaseConfiguration($_ENV['JAWSDB_URL']);
		} else {
			throw new AutoConfigurationException('No database connection configuration detected.', 1430418305);
		}
	}

	static protected function updateDatabaseConfiguration($databaseUrl)
	{

		$dbParts = parse_url($databaseUrl);
		if ($dbParts === false || $dbParts['scheme'] !== 'mysql') {
			throw new AutoConfigurationException('The database connection does not seem to be MySQL compatible',
				1430418306);
		}
		$GLOBALS['TYPO3_CONF_VARS']['DB']['username'] = empty($dbParts['user']) ? '' : $dbParts['user'];
		$GLOBALS['TYPO3_CONF_VARS']['DB']['password'] = empty($dbParts['pass']) ? '' : $dbParts['pass'];
		$GLOBALS['TYPO3_CONF_VARS']['DB']['host'] = empty($dbParts['host']) ? 'localhost' : $dbParts['host'];
		$GLOBALS['TYPO3_CONF_VARS']['DB']['port'] = empty($dbParts['port']) ? 3306 : $dbParts['port'];
		$GLOBALS['TYPO3_CONF_VARS']['DB']['database'] = trim($dbParts['path'], '/') === '' ? '' : trim($dbParts['path'],
			'/');
	}

	static public function configureSessionStorage()
	{

		if (!empty($_ENV['MEMCACHED_SERVERS'])) {
			static::updateSessionHandlerConfiguration($_ENV['MEMCACHED_SERVERS'], $_ENV['MEMCACHED_USERNAME'],
				$_ENV['MEMCACHED_PASSWORD']);
		} elseif (!empty($_ENV['MEMCACHIER_SERVERS'])) {
			static::updateSessionHandlerConfiguration($_ENV['MEMCACHIER_SERVERS'], $_ENV['MEMCACHIER_USERNAME'],
				$_ENV['MEMCACHIER_PASSWORD']);
		} elseif (!empty($_ENV['MEMCACHEDCLOUD_SERVERS'])) {
			static::updateSessionHandlerConfiguration($_ENV['MEMCACHEDCLOUD_SERVERS'], $_ENV['MEMCACHEDCLOUD_USERNAME'],
				$_ENV['MEMCACHEDCLOUD_PASSWORD']);
		}
	}

	static protected function updateSessionHandlerConfiguration($servers, $username, $password)
	{

		ini_set('session.save_handler', 'memcached');
		ini_set('session.save_path', 'PERSISTENT=typo3_session ' . $servers);
		ini_set('memcached.sess_binary', 1);
		ini_set('memcached.sess_sasl_username', $username);
		ini_set('memcached.sess_sasl_password', $password);
	}

	static public function relaxBackendIpRestriction()
	{

		if (!empty($_ENV['DYNO']) && preg_match('#^web\.\d+$#', $_ENV['DYNO'])) {
			$GLOBALS['TYPO3_CONF_VARS']['BE']['lockIP'] = 1;
		}
	}
}
