<?php
/**
 * @version   $Id: rokquickcart.php 6852 2013-01-28 18:51:50Z btowles $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('_JEXEC') or die;
include_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/legacy_class.php');

jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT . '/helpers/route.php';

$controller = RokQuickCartLegacyJController::getInstance('RokQuickCart');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
