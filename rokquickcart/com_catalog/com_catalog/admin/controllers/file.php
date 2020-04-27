<?php
/**
 * @version   $Id: file.php 6852 2013-01-28 18:51:50Z btowles $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * @package     RocketTheme
 * @subpackage  rokquickcart.controllers
 */
class RokQuickCartControllerFile extends RokQuickCartController
{

	/**
	 * Upload a file
	 */
	function upload()
	{
		$app = JFactory::getApplication();

		// Check for request forgeries
		JSession::checkToken('request') or jexit('Invalid Token');

		$file   = JFactory::getApplication()->input->get('Filedata', '', 'array');
		$folder = JFactory::getApplication()->input->getPath('folder', '');
		$format = JFactory::getApplication()->input->getCmd('format', 'html');
		$return = JFactory::getApplication()->input->getBase64('return-url', null);
		$err    = null;

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);

		if (isset($file['name'])) {
			$filepath = JPath::clean(COM_ROKQUICKCART_BASE . '/' . $folder . '/' . strtolower($file['name']));

			if (!MediaHelper::canUpload($file, $err)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'Invalid: ' . $filepath . ': ' . $err));
					header('HTTP/1.0 415 Unsupported Media Type');
					jexit('Error. Unsupported Media Type!');
				} else {
					JError::raiseNotice(100, JText::_($err));
					// REDIRECT
					if ($return) {
						$app->redirect(base64_decode($return) . '&folder=' . $folder);
					}
					return;
				}
			}

			if (JFile::exists($filepath)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'File already exists: ' . $filepath));
					header('HTTP/1.0 409 Conflict');
					jexit('Error. File already exists');
				} else {
					JError::raiseNotice(100, JText::_('Error. File already exists'));
					// REDIRECT
					if ($return) {
						$app->redirect(base64_decode($return) . '&folder=' . $folder);
					}
					return;
				}
			}

			if (!JFile::upload($file['tmp_name'], $filepath)) {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = JLog::getInstance('upload.error.php');
					$log->addEntry(array('comment' => 'Cannot upload: ' . $filepath));
					header('HTTP/1.0 400 Bad Request');
					jexit('Error. Unable to upload file');
				} else {
					JError::raiseWarning(100, JText::_('Error. Unable to upload file'));
					// REDIRECT
					if ($return) {
						$app->redirect(base64_decode($return) . '&folder=' . $folder);
					}
					return;
				}
			} else {
				if ($format == 'json') {
					jimport('joomla.error.log');
					$log = JLog::getInstance();
					$log->addEntry(array('comment' => $folder));
					jexit('Upload complete');
				} else {
					$app->enqueueMessage(JText::_('Upload complete'));
					// REDIRECT
					if ($return) {
						$app->redirect(base64_decode($return) . '&folder=' . $folder);
					}
					return;
				}
			}
		} else {
			$app->redirect('index.php', 'Invalid Request', 'error');
		}
	}

	/**
	 * Deletes paths from the current path
	 *
	 * @param string $listFolder The image directory to delete a file from
	 *
	 * @since 1.5
	 */
	function delete()
	{
		$app = JFactory::getApplication();

		JSession::getFormToken('request') or jexit('Invalid Token');

		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');

		// Get some data from the request
		$tmpl   = JFactory::getApplication()->input->getString('tmpl');
		$paths  = JFactory::getApplication()->input->get('rm', array(), 'array');
		$folder = JFactory::getApplication()->input->getString('folder');

		// Initialize variables
		$msg = array();
		$ret = true;

		if (count($paths)) {
			foreach ($paths as $path) {
				if ($path !== JFile::makeSafe($path)) {
					JError::raiseWarning(100, JText::_('Unable to delete:') . htmlspecialchars($path, ENT_COMPAT, 'UTF-8') . ' ' . JText::_('WARNFILENAME'));
					continue;
				}

				$fullPath = JPath::clean(COM_ROKQUICKCART_BASE . '/' . $folder . '/' . $path);
				if (is_file($fullPath)) {
					$ret |= !JFile::delete($fullPath);
				} else if (is_dir($fullPath)) {
					$files     = JFolder::files($fullPath, '.', true);
					$canDelete = true;
					foreach ($files as $file) {
						if ($file != 'index.html') {
							$canDelete = false;
						}
					}
					if ($canDelete) {
						$ret |= !JFolder::delete($fullPath);
					} else {
						JError::raiseWarning(100, JText::_('Unable to delete:') . $fullPath . ' ' . JText::_('Not Empty!'));
					}
				}
			}
		}
		if ($tmpl == 'component') {
			// We are inside the iframe
			$app->redirect('index.php?option=com_rokquickcart&view=mediaList&folder=' . $folder . '&tmpl=component');
		} else {
			$app->redirect('index.php?option=com_rokquickcart&folder=' . $folder);
		}
	}
}
