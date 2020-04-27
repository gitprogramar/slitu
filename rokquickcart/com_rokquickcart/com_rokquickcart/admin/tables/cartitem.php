<?php
/**
 * @version   $Id: cartitem.php 6852 2013-01-28 18:51:50Z btowles $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.path');

/**
 * @package        Joomla.Administrator
 * @subpackage     com_contact
 */
class RokQuickCartTableCartItem extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = 0;

	/**
	 * @var text
	 */
	var $name = null;

	/**
	 * @var text
	 */
	var $description = null;

	/**
	 * @var null
	 */
	var $image = null;

	/**
	 * @var float
	 */
	var $price = 0;

	/**
	 * @var null
	 */
	var $shipping = null;

	/**
	 * @var int
	 */
	var $published = 0;

	/**
	 * @var boolean
	 */
	var $checked_out = 0;

	/**
	 * @var time
	 */
	var $checked_out_time = 0;

	/**
	 * @var int
	 */
	var $ordering = 0;

	/**
	 * @var null
	 */
	var $params = null;
	
	/**
	 * @var int
	 */
	var $asset_id = 0;
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 *
	 * @since 1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__rokquickcart', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @param    array        Named array
	 *
	 * @return    null|string    null is operation was satisfactory, otherwise returns an error
	 * @since    1.6
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string)$registry;
		}
		
		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded store function
	 *
	 * @param    boolean    True to update fields even if they are null.
	 *
	 * @return    boolean    True on success, false on failure.
	 * @since    1.6
	 */
	public function store($updateNulls = false)
	{
		// Transform the params field
		if (is_array($this->params)) {
			$registry = new JRegistry();
			$registry->loadArray($this->params);
			$this->params = (string)$registry;
		}
		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	/**
	 * Overloaded check function
	 *
	 * @return boolean
	 * @see   JTable::check
	 * @since 1.5
	 */
	//TODO Add checks for price and shipping values
	/**
	 * @return bool
	 */
	function check()
	{
		/** check for valid name */
		if (trim($this->name) == '') {
			$this->setError(JText::_('ROKQUICKCART_ERR_NAME_MISSING'));
			return false;
		}

		/** check for valid price */
		if (trim($this->price) == '' || !is_float((float)trim($this->price))) {
			$this->setError(JText::_('ROKQUICKCART_ERR.VALID_PRICE'));
			return false;
		}

		/** check for valid shipping */
		/*
		if (trim($this->shipping) != '' && !is_float((float)trim($this->shipping))) {
			$this->setError(JText::_('ROKQUICKCART_ERR.VALID_SHIPPING'));
			return false;
		}
		*/
		return true;
	}
}
