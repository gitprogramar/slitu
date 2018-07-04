<?php
/**
 * @version   $Id: rokquickcart_install.script.php 19340 2014-02-28 21:53:32Z btowles $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of HelloWorld component
 */
class com_RokQuickCartInstallerScript
{
	public function update($parent)
	{
		$old_lang_file = JPATH_SITE . '/language/en-GB/en-GB.com_rokquickcart.ini';
		if (@file_exists($old_lang_file) && is_writable($old_lang_file)) {
			@unlink($old_lang_file);
		}
		return true;
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @param $type
	 * @param $parent
	 *
	 * @return void
	 */
	public function postflight($type, $parent)
	{
		if ($type == 'install') {
			$this->installFromFile($parent, '/install/sampledata.sql');
		}
	}

	/**
	 * @param $parent JInstallerComponent
	 */
	protected function installFromFile($parent, $path = false)
	{
		if ($path === false) {
			JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_SQL_NO_PATH'));

			return false;
		}

		$installer = $parent->getParent();
		$db        = JFactory::getDbo();

		// Initialise variables.
		$queries = array();

		$sqlfile = $installer->getPath('extension_root') . $path;

		// Check that sql files exists before reading. Otherwise raise error for rollback
		if (!file_exists($sqlfile)) {
			JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_FILENOTFOUND', $sqlfile));

			return false;
		}

		$buffer = file_get_contents($sqlfile);

		// Graceful exit and rollback if read not successful
		if ($buffer === false) {
			JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'));

			return false;
		}

		// Create an array of queries from the sql file
		jimport('joomla.installer.helper');
		$queries = JInstallerHelper::splitSql($buffer);

		if (count($queries) == 0) {
			// No queries to process
			return 0;
		}

		// Process each query in the $queries array (split out of sql file).
		foreach ($queries as $query) {
			$query = trim($query);

			if ($query != '' && $query{0} != '#') {
				$db->setQuery($query);

				if (!$db->query()) {
					JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));

					return false;
				}
			}
		}


		return (int)count($queries);
	}
}