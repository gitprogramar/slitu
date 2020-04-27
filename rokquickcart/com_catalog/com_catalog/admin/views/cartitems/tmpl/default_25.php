<?php
/**
 * @version   $Id: default_25.php 6852 2013-01-28 18:51:50Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2016 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$canOrder = $user->authorise('core.edit.state', 'com_rokquickcart');
$saveOrder = $listOrder == 'a.ordering';
?>

<form action="<?php echo JRoute::_('index.php?option=com_rokquickcart'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('ROKQUICKCART_SEARCH_IN_NAME'); ?>"/>
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', RokQuickCartHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.state'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
		<tr>
			<th width="1%">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
			</th>
			<th>
				<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
			</th>
			<th width="5%">
				<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
			</th>
			<th width="10%">
				<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
				<?php if ($canOrder && $saveOrder) : ?>
					<?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'cartitems.saveorder'); ?>
				<?php endif; ?>
			</th>
			<th width="1%" class="nowrap">
				<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.lang_id', $listDirn, $listOrder); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="10">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php
		$n = count($this->items);
		foreach ($this->items as $i => $item) :
			$ordering   = $listOrder == 'a.ordering';
			$canCreate  = $user->authorise('core.create', 'com_rokquickcart');
			$canEdit    = $user->authorise('core.edit', 'com_rokquickcart.cartitem.' . $item->id);
			$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canChange  = $user->authorise('core.edit.state', 'com_rokquickcart.cartitem.' . $item->id) && $canCheckin;

			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'cartitems.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit || $canEditOwn) : ?>
						<?php $img_path = RokQuickCartHelper::getCartImage($item->image); ?>
						<span class="editlinktip hasTip" title="<?php echo JText::_('ROKQUICKCART_EDIT_ITEM');?>::<?php echo $this->escape($item->name); ?><br/>
                        <img border=&quot;1&quot; src=&quot;<?php echo $img_path; ?>&quot; name=&quot;imagelib&quot; alt=&quot;<?php echo JText::_('No preview available'); ?>&quot;/>">
                        <a href="<?php echo JRoute::_('index.php?option=com_rokquickcart&task=cartitem.edit&id=' . (int)$item->id); ?>"><?php echo $this->escape($item->name); ?></a></span>
					<?php else : ?>
						<span class="editlinktip hasTip" title="Tooltip title::This is the tooltip text">
						<?php $img_path = RokQuickCartHelper::getCartImage($item->image); ?>
							<span class="editlinktip hasTip" title="<?php echo JText::_('ROKQUICKCART_EDIT_ITEM');?>::<?php echo $this->escape($item->name); ?><br/>
                        <img border=&quot;1&quot; src=&quot;<?php echo $img_path; ?>&quot; name=&quot;imagelib&quot; alt=&quot;<?php echo JText::_('No preview available'); ?>&quot;/>">
                        <?php echo $this->escape($item->name); ?></span>
                        </span>
					<?php endif; ?>
				</td>
				<td align="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'cartitems.', $canChange, 'cb'); ?>
				</td>
				<td class="order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) : ?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, true, 'cartitems.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'cartitems.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, true, 'cartitems.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'cartitems.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order"/>
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<td align="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

