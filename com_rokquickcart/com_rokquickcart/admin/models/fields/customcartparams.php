<?php
/**
 * @version   $Id: customcartparams.php 19417 2014-03-03 19:13:31Z djamil $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2015 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Renders a file list from a directory in the current templates directory
 */

/**
 * @package     RocketTheme
 * @subpackage  rokquickcart.libs.elements
 */
class JFormFieldCustomCartParams extends JFormField
{

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'CustomCartParams';

	function getInput()
	{
		$doc    		 = JFactory::getDocument();
		$name   		 = (string) $this->element['name'];

		if (empty($this->value)) $this->_convertOldParameters();
		$html = array();

		$html[] = '<button class="btn btn-primary" data-rqc-addblock>Agregar</button><div class="clr"></div>' . '<div id="div-' . $name . '" class="div-customfields">';

		/*if (!empty($backwardsCompat)){
			foreach ($backwardsCompat as $key => $value) {
				$html[] = '<div class="rokquickcart-extendedlink" style="margin: 5px 0pt;">';
				$html[] = '		<div class="rokquickcart-key"><input type="text" name="'. $this->name . '[' . $key . ']" placeholder="ie, Colors, Sizes" value="'.$key.'" /></div>';
				$html[] = '		<div class="rokquickcart-value">';
				foreach ($value as $field_value) {
					$html[]	= '			<input type="text" name="'. $this->name . '[' . $key . '][]" value="'. $field_value .'" />';
				}
				$html[] = '		</div>';
				$html[] = '</div>';
			}
		}*/

		if (!empty($this->value)){
			foreach ($this->value as $key => $value) {
				$index = 1;
				if (empty($value)) continue;
				$html[] = '<div class="rokquickcart-field-block">';
				$html[] = '		<div class="rokquickcart-key">';
				$html[] = '			<input type="text" name="'. $this->name . '[' . $key . ']" placeholder="Nombre de categoría" value="'.$key.'" />';
				$html[] = '			<span class="btn" data-rqc-remblock><i class="icon-minus-2"></i></span>';
				$html[] = '		</div>';
				$html[] = '		<div class="rokquickcart-value" style="display: none;">';
				foreach ($value as $field_value) {
					$html[] = '		<div class="rokquickcart-value-item">';
					$html[] = '			<span class="rokquickcart-option">Option ' . $index++ . '</span>';
					$html[]	= '			<input type="text" name="'. $this->name . '[' . $key . '][]" value="'. $field_value .'" class="input-small" placeholder="ie, Green, Blue, ..." />';
					$html[] = '			<span class="btn-group">';
					$html[] = '				<span class="btn btn-mini" data-rqc-addrow><i class="icon-plus-2"></i></span>';
					$html[] = '				<span class="btn btn-mini" data-rqc-remrow><i class="icon-minus-2"></i></span>';
					$html[] = '			</span>';
					$html[] = '		</div>';
				}
				$html[] = '		</div>';
				$html[] = '</div>';
			}
		}
		$html[] = '</div>';

		$plugin_js_path = JURI::root(true) . '/administrator/components/com_rokquickcart/libs/js';
		$doc->addScript($plugin_js_path . "/rokquickcartsparams.js");
		$doc->addScriptDeclaration("window.addEvent('domready', function() { new RokQuickCartParams({container: 'div-".$name."', basename: '" . $name . "', params: 'jform[params]', paramsid: 'jform_params_'}); });");

		return implode("\n", $html);
	}

	function getLabel(){
		return '<h2>'.JText::_($this->element['label']).'</h2>';
	}

	function _convertOldParameters(){
		$backwardsCompat = array();

		/*
		array (size=2)
		  'Sizes' =>
		    array (size=3)
		      0 => string 'Small' (length=5)
		      1 => string 'Medium' (length=6)
		      2 => string 'Large' (length=5)
		  'Colors' =>
		    array (size=4)
		      0 => string 'Black' (length=5)
		      1 => string 'Red' (length=3)
		      2 => string 'Blue' (length=4)
		      3 => string 'Grey' (length=4)

		*/

		foreach ($this->form->_old_params as $key => $value) {
			if (preg_match("/^has_/i", $key)) continue;
			$backwardsCompat[ucfirst($key)] = $value;
		}

		if (empty($backwardsCompat)) return false;

    	$this->value = (!$this->value) ? $backwardsCompat : array_merge($this->value, $backwardsCompat);

		return true;
	}
}
