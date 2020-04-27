<?php
/**
 * @version   $Id: cartitems.php 6852 2013-01-28 18:51:50Z btowles $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Articles list controller class.
 *
 * @package        Joomla.Administrator
 * @subpackage     com_contact
 * @since          1.6
 */
class RokQuickCartControllerCartItems extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param    array    $config    An optional associative array of configuration settings.
	 *
	 * @return    ContactControllerContacts
	 * @see        JController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param    string    $name      The name of the model.
	 * @param    string    $prefix    The prefix for the PHP class name.
	 *
	 * @return    JModel
	 * @since    1.6
	 */
	public function getModel($name = 'CartItem', $prefix = 'RokQuickCartModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}