<?php
/**
 * @version   $Id: number.php 6852 2013-01-28 18:51:50Z btowles $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


jimport('joomla.form.formrule');
jimport('joomla.language.language');

class JFormRuleNumber extends JFormRule
{
	public function test(& $element, $value, $group = null, & $input = null, & $form = null)
	{

		$option = 'com_rokquickcart';
		$lang   = JFactory::getLanguage();
		$lang->load($option, JPATH_BASE, null, false, false) || $lang->load($option, JPATH_BASE . "/components/$option", null, false, false) || $lang->load($option, JPATH_BASE, $lang->getDefault(), false, false) || $lang->load($option, JPATH_BASE . "/components/$option", $lang->getDefault(), false, false);

		$min = null;
		$max = null;
		// If the field is empty and not required, the field is valid.
		$required = ((string)$element['required'] == 'true' || (string)$element['required'] == 'required');
		if (!$required && empty($value)) {
			return true;
		}

		if (!is_numeric($value)) {
			return false;
		}

		return true;
	}
}
