<?php
/**
 * @version   $Id: controller.php 6852 2013-01-28 18:51:50Z btowles $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
include_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/legacy_class.php');

/**
 * Component Controller
 *
 * @package        Joomla.Administrator
 * @subpackage     com_contact
 */
class RokquickcartController extends RokQuickCartLegacyJController
{
	/**
	 * @var        string    The default view.
	 * @since    1.6
	 */
	protected $default_view = 'cartitems';

	/**
	 * Method to display a view.
	 *
	 * @param    boolean            If true, the view output will be cached
	 * @param    array              An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return    JController        This object to support chaining.
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT . '/helpers/rokquickcart.php';

		$view   = JFactory::getApplication()->input->get('view', 'cartitems');
		$layout = JFactory::getApplication()->input->get('layout', 'default');
		$id     = JFactory::getApplication()->input->getInt('id');

		// Check for edit form.
		if ($view == 'cartitem' && $layout == 'edit' && !$this->checkEditId('com_rokquickcart.edit.cartitem', $id)) {

			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_rokquickcart&view=cartitems', false));

			return false;
		}

		parent::display();

		return $this;
	}
}