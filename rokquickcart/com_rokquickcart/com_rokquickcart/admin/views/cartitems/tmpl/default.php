<?php
/**
 * @version   $Id: default.php 6852 2013-01-28 18:51:50Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// no direct access
defined('_JEXEC') or die;

$version = new JVersion();

if (version_compare($version->getShortVersion(), '3.0', '>=')) {
	echo $this->loadTemplate('30');
} else {
	echo $this->loadTemplate('25');
}