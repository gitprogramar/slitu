<?php
/**
 * @version   $Id: legacy_class.php 6852 2013-01-28 18:51:50Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('_JEXEC') or die('Restricted access');

if (!class_exists('RokQuickCartLegacyJView', false)) {
	$jversion = new JVersion();
	if (version_compare($jversion->getShortVersion(), '2.5.5', '>')) {
		class RokQuickCartLegacyJView extends JViewLegacy
		{
		}

		class RokQuickCartLegacyJController extends JControllerLegacy
		{
		}

		class RokQuickCartLegacyJModel extends JModelLegacy
		{
		}
	} else {
		jimport('joomla.application.component.view');
		jimport('joomla.application.component.controller');
		jimport('joomla.application.component.model');
		class RokQuickCartLegacyJView extends JView
		{
		}

		class RokQuickCartLegacyJController extends JController
		{
		}

		class RokQuickCartLegacyJModel extends JModel
		{
		}
	}
}
