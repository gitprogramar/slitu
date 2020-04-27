<?php
/**
 * @version   $Id: view.html.php 19276 2014-02-28 03:47:35Z djamil $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/rokquickcart.php';
jimport('joomla.application.component.view');
jimport('joomla.html.pane');
include_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/legacy_class.php');

/**
 * @package     RocketTheme
 * @subpackage  rokquickcart.views.cartitem
 */
class RokQuickCartViewCartItem extends RokQuickCartLegacyJView
{

	protected $form;
	protected $item;
	protected $state;


	function display($tpl = null)
	{
		jimport('joomla.form.form');
		$option = JFactory::getApplication()->input->get('option');
		$doc    = JFactory::getDocument();
		$version = new JVersion();

		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$doc->addStyleSheet('components/com_rokquickcart/assets/rokquickcart.css');
		if (version_compare($version->getShortVersion(), '3.0', '<')) {
			$doc->addStyleSheet('components/com_rokquickcart/assets/rokquickcart-j25.css');
		}
		$com_params      = JComponentHelper::getParams($option);
		$image_width     = $com_params->get('shelf_image_width', 100);
		$cart_images_dir = 'images/rokquickcart/';
		$default_image   = $com_params->get('default_image', 'images/rokquickcart/samples/noimage.png');

		$this->assignRef('cart_images_dir', $cart_images_dir);
		$this->assignRef('default_image', $default_image);
		$this->assignRef('image_width', $image_width);
		$this->assignRef('image', $this->item->image);

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', 1);
		$isNew      = ($this->item->id == 0);
		$user       = JFactory::getUser();
		$userId     = $user->get('id');
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo      = RokQuickCartHelper::getActions();

		$text = $isNew ? JText::_('NEW') : JText::_('EDIT');
		JToolBarHelper::title(JText::_('ROKQUICKCART_CARTITEM') . ': <small><small>[ ' . $text . ' ]</small></small>', 'rokquickcart.png');

		// Build the actions for new and existing records.
		if ($isNew) {
			// For new records, check the create permission.
			if ($isNew) {
				JToolBarHelper::apply('cartitem.apply');
				JToolBarHelper::save('cartitem.save');
				JToolBarHelper::save2new('cartitem.save2new');
			}
			JToolBarHelper::cancel('cartitem.cancel');
		} else {
			// Can't save the record if it's checked out.
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
					JToolBarHelper::apply('cartitem.apply');
					JToolBarHelper::save('cartitem.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create')) {
						JToolBarHelper::save2new('cartitem.save2new');
					}
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create')) {
				JToolBarHelper::save2copy('cartitem.save2copy');
			}

			JToolBarHelper::cancel('cartitem.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_ROKQUICKCART');
	}
}
