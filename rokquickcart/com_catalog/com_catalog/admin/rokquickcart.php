<?php
/**
 * @version   $Id: rokquickcart.php 6852 2013-01-28 18:51:50Z btowles $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('_JEXEC') or die('Restricted access');


// Require the base controller
require_once (JPATH_COMPONENT . '/controller.php');

// Load the admin HTML view
require_once (JPath::clean(JPATH_ADMINISTRATOR . '/components/com_media/helpers/media.php'));

define('COM_ROKQUICKCART_BASE', JPATH_ROOT . '/images/rokquickcart');
define('COM_ROKQUICKCART_BASEURL', JURI::root() . 'images/rokquickcart');

$cmd = JFactory::getApplication()->input->get('task', null);

if (strpos($cmd, '.') != false) {
	// We have a defined controller/task pair -- lets split them out
	list($controllerName, $task) = explode('.', $cmd);

	// Define the controller name and path
	$controllerName = strtolower($controllerName);
	$controllerPath = JPATH_COMPONENT . '/controllers/' . $controllerName . '.php';

	// If the controller file path exists, include it ... else lets die with a 500 error
	if (file_exists($controllerPath)) {
		require_once($controllerPath);
	} else {
		JError::raiseError(500, 'Invalid Controller');
	}
} else {
	// Base controller, just set the task :)
	$controllerName = null;
	$task           = $cmd;
}
// Set the name for the controller and instantiate it
$controllerClass = 'RokQuickCartController' . ucfirst($controllerName);

if (class_exists($controllerClass)) {
	$controller = new $controllerClass();
} else {
	JError::raiseError(500, 'Invalid Controller Class');
}

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();