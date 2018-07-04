<?php
/**
 * @version   $Id: view.html.php 19276 2014-02-28 03:47:35Z djamil $
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

include_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/legacy_class.php');

require_once JPATH_COMPONENT . '/helpers/rokquickcart.php';


/**
 * @package     RocketTheme
 * @subpackage  rokquickcart.views.rokquickcart
 */
class rokquickcartViewcartitems extends RokQuickCartLegacyJView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return    void
	 */
	public function display($tpl = null)
	{
		$doc = JFactory::getDocument();

		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state      = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$item->order_up = true;
			$item->order_dn = true;
		}

		$doc->addStyleSheet('components/com_rokquickcart/assets/rokquickcart.css');

		$this->assignRef('image', $this->item->image);

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
			$version = new JVersion();
			if (version_compare($version->getShortVersion(), '3.0', '>=')) {
				$this->sidebar = JHtmlSidebar::render();
			}
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = RokQuickCartHelper::getActions('com_rokquickcart', 'category', $state->get('filter.category_id'));
		$user  = JFactory::getUser();
		JToolBarHelper::title(JText::_('ROKQUICKCART_MANAGER_ITEMS'), 'rokquickcart.png');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_rokquickcart', 'core.create'))) > 0) {
			JToolBarHelper::addNew('cartitem.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('cartitem.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('cartitems.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('cartitems.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('cartitems.archive');
			JToolBarHelper::checkin('cartitems.checkin');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'cartitems.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} elseif ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('cartitems.trash');
			JToolBarHelper::divider();
		}


		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_rokquickcart');
			JToolBarHelper::divider();
		}

		JToolBarHelper::help('JHELP_COMPONENTS_ROKQUICKCART');

		$version = new JVersion();
		if (version_compare($version->getShortVersion(), '3.0', '>=')) {
			JHtmlSidebar::setAction('index.php?option=com_rokquickcart');

			JHtmlSidebar::addFilter(JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_state', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true));
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering'  => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.name'      => JText::_('JGLOBAL_TITLE'),
			'a.id'        => JText::_('JGRID_HEADING_ID')
		);
	}

}
