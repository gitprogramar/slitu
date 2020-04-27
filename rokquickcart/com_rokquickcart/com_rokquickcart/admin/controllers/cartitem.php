<?php
/**
 * @version   $Id: cartitem.php 6852 2013-01-28 18:51:50Z btowles $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * @package        Joomla.Administrator
 * @subpackage     com_contact
 * @since          1.6
 */
class RokQuickCartControllerCartItem extends JControllerForm
{
	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param    array    $data    An array of input data.
	 *
	 * @return    boolean
	 * @since    1.6
	 */
	protected function allowAdd($data = array())
	{
		return JFactory::getUser()->authorise('core.create', $this->extension);
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param    array     $data    An array of input data.
	 * @param    string    $key     The name of the key for the primary key.
	 *
	 * @return    boolean
	 * @since    1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$user = JFactory::getUser();

		// Check general edit permission first.
		if ($user->authorise('core.edit', $this->option)) {
			return true;
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}
}